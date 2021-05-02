<?php

namespace App\Controller;

use App\Entity\EggsInputs;
use App\Entity\EggsSelections;
use App\Entity\EggSupplier;
use App\Form\EggsSelectionsType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsSelectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_selections")
 * @IsGranted("ROLE_USER")
 */
class SelectionsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_selections_index", methods={"GET"})
     */
    public function index(EggsSelectionsRepository $eggsSelectionsRepository): Response
    {
        return $this->render('eggs_selections/index.html.twig', [
            'eggs_selections' => $eggsSelectionsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_selections_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EggsInputsDetailsRepository $detailsRepository): Response
    {
        $form = $this->createForm(EggsSelectionsType::class);
        $form->handleRequest($request);

        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            $form['breeder']->getData() instanceof EggSupplier &&
            $form['eggsInputs']->getData() instanceof EggsInputs
        ) {
            $totalEggs = 0;
            $inputs = $form['eggsInputs']->getData();
            $breeder = $form['breeder']->getData();
            $chickNumber = $form['chickNumber']->getData();
            $cullChicken = $form['cullChicken']->getData();
            $selectionDate = $form['selectionDate']->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $inputsDetails = $detailsRepository->inputBreederDetails($inputs, $breeder);

            /** @var  $inputDetail
             * Get sum eggs for breeder in eggs input
             */
            foreach ($inputsDetails as $inputDetail) {
                $totalEggs = $totalEggs + $inputDetail[1];
            }

            /** @var  $inputDetail
             * Added eggs transfer to eggs input details
             */
            foreach ($inputsDetails as $inputDetail) {
                $eggsSelection = new EggsSelections();
                $eggsSelection->setEggsInputsDetail($inputDetail[0]);
                $eggsSelection->setChickNumber($inputDetail[1] / $totalEggs * $chickNumber);
                $eggsSelection->setCullChicken($inputDetail[1] / $totalEggs * $cullChicken);
                $eggsSelection->setSelectionDate($selectionDate);
                $entityManager->persist($eggsSelection);
            }

            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_selections/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_selections_show", methods={"GET"})
     */
    public function show(EggsSelections $eggsSelection): Response
    {
        return $this->render('eggs_selections/show.html.twig', [
            'eggs_selection' => $eggsSelection,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_selections_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsSelections $eggsSelection): Response
    {
        $form = $this->createForm(EggsSelectionsType::class, $eggsSelection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_selections_index');
        }

        return $this->render('eggs_selections/edit.html.twig', [
            'eggs_selection' => $eggsSelection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_selections_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggsSelections $eggsSelection): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggsSelection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsSelection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_selections_index');
    }
}
