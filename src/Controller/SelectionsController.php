<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Entity\Selections;
use App\Entity\Supplier;
use App\Form\EggsSelectionsType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsRepository;
use App\Repository\EggsSelectionsRepository;
use App\Repository\EggSupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/selections")
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
     * @Route("/new/{inputs}/{breeder}", name="eggs_selections_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new($inputs,
                        $breeder,
                        Request $request,
                        EggsInputsDetailsRepository $detailsRepository,
                        EggsInputsRepository $eggsInputsRepository,
                        EggSupplierRepository $eggSupplierRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $breeder = $eggSupplierRepository->find($breeder);
        $form = $this->createForm(EggsSelectionsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalEggs = 0;
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

            $length = count($inputsDetails) - 1;
            $totalChick = 0;
            $totalCull = 0;

            /** @var  $inputDetail
             * Added eggs transfer to eggs input details
             */
            foreach ($inputsDetails as $key => $inputDetail) {
                $eggsSelection = new Selections();
                $eggsSelection->setEggsInputsDetail($inputDetail[0]);
                if ($key < $length) {
                    $setChick = round($inputDetail[1] / $totalEggs * $chickNumber, 0);
                    $setCull = round($inputDetail[1] / $totalEggs * $cullChicken, 0);
                    $eggsSelection->setChickNumber($setChick);
                    $eggsSelection->setCullChicken($setCull);
                    $totalChick = $totalChick + $setChick;
                    $totalCull = $totalCull + $setCull;
                } else {
                    dump($chickNumber);
                    $setChick = $chickNumber - $totalChick;
                    $setCull = $cullChicken - $totalCull;
                    $eggsSelection->setChickNumber($setChick);
                    $eggsSelection->setCullChicken($setCull);
                }
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
    public function show(Selections $eggsSelection): Response
    {
        return $this->render('eggs_selections/show.html.twig', [
            'eggs_selection' => $eggsSelection,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_selections_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Selections $eggsSelection): Response
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
    public function delete(Request $request, Selections $eggsSelection): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsSelection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsSelection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_selections_index');
    }
}
