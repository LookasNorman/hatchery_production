<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\Selections;
use App\Entity\Transfers;
use App\Form\SelectionsType;
use App\Repository\HerdsRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\InputsRepository;
use App\Repository\SelectionsRepository;
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
    public function index(SelectionsRepository $eggsSelectionsRepository): Response
    {
        return $this->render('eggs_selections/index.html.twig', [
            'eggs_selections' => $eggsSelectionsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{inputs}/{farm}/{herd}", name="eggs_selections_new", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function new(
        Inputs $inputs,
        InputsFarm $farm,
        Herds $herd,
        Request $request,
        InputsFarmDeliveryRepository $inputsFarmDeliveryRepository
    ): Response
    {
        $form = $this->createForm(SelectionsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalEggs = 0;
            $chickNumber = $form['chickNumber']->getData();
            $cullChickPercent = $form['cullChickenPercent']->getData();
            $selectionDate = $form['selectionDate']->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $inputsFarmDelivery = $inputsFarmDeliveryRepository->herdInputsFarmInInput($farm, $herd);

            foreach ($inputsFarmDelivery as $inputFarmDelivery) {
                $totalEggs = $totalEggs
                    + $inputFarmDelivery->getEggsNumber()
                    - round($inputFarmDelivery->getLighting()->getWasteEggs() / $inputFarmDelivery->getLighting()->getLightingEggs() + 0.02, 3)
                    * $inputFarmDelivery->getEggsNumber();
            }

            $totalChick = 0;
            $length = count($inputsFarmDelivery) - 1;

            foreach ($inputsFarmDelivery as $key => $inputFarmDelivery) {
                $eggsInputSelections = new Selections();
                $eggsInputSelections->setSelectionDate($selectionDate);

                if ($key < $length) {
                    $setChick = round($chickNumber / $totalEggs * $inputFarmDelivery->getTransfers()->getTransfersEgg());
                    $eggsInputSelections->setChickNumber($setChick);
                    $totalChick = $totalChick + $setChick;

                } else {
                    $setChick = $chickNumber - $totalChick;
                    $eggsInputSelections->setChickNumber($setChick);
                }
                $setCullChick = round($setChick * $cullChickPercent, 0);
                $eggsInputSelections->setCullChicken($setCullChick);

                $setUnhatched = $inputFarmDelivery->getEggsNumber() - $inputFarmDelivery->getLighting()->getWasteLighting() - $setChick - $setCullChick;
                $eggsInputSelections->setUnhatched($setUnhatched);

                $eggsInputSelections->addInputsFarmDelivery($inputFarmDelivery);

                $entityManager->persist($eggsInputSelections);
            }


            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_selections/new.html.twig', [
            'form' => $form->createView(),
            'input' => $inputs,
            'herd' => $herd
        ]);
    }

    /**
     * @param \App\Repository\InputsRepository $inputsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfer", name="no_selection_index", methods={"GET"})
     */
    public function showNoSelection(InputsRepository $inputsRepository): Response
    {
        $selections = $inputsRepository->inputsNoSelection();
        return $this->render('eggs_inputs/no_selection.html.twig', [
            'eggs_inputs' => $selections,
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
        $form = $this->createForm(SelectionsType::class, $eggsSelection);
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
