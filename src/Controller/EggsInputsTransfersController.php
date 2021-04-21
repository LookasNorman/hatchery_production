<?php

namespace App\Controller;

use App\Entity\EggsInputs;
use App\Entity\EggsInputsTransfers;
use App\Entity\EggSupplier;
use App\Form\EggsInputsTransfersType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsTransfersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_inputs_transfers")
 * @IsGranted("ROLE_USER")
 */
class EggsInputsTransfersController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_transfers_index", methods={"GET"})
     */
    public function index(EggsInputsTransfersRepository $eggsInputsTransfersRepository): Response
    {
        return $this->render('eggs_inputs_transfers/index.html.twig', [
            'eggs_inputs_transfers' => $eggsInputsTransfersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_inputs_transfers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EggsInputsDetailsRepository $detailsRepository): Response
    {
        $form = $this->createForm(EggsInputsTransfersType::class);
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
            $wasteEggs = $form['wasteEggs']->getData();
            $lightingDate = $form['transferDate']->getData();
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
                $eggsInputsTransfer = new EggsInputsTransfers();
                $eggsInputsTransfer->setEggsInputsDetail($inputDetail[0]);
                $eggsInputsTransfer->setWasteEggs($inputDetail[1] / $totalEggs * $wasteEggs);
                $eggsInputsTransfer->setTransferDate($lightingDate);
                $entityManager->persist($eggsInputsTransfer);
            }

            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_transfers_index');
        }

        return $this->render('eggs_inputs_transfers/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_transfers_show", methods={"GET"})
     */
    public function show(EggsInputsTransfers $eggsInputsTransfer): Response
    {
        return $this->render('eggs_inputs_transfers/show.html.twig', [
            'eggs_inputs_transfer' => $eggsInputsTransfer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_transfers_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsInputsTransfers $eggsInputsTransfer): Response
    {
        $form = $this->createForm(EggsInputsTransfersType::class, $eggsInputsTransfer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_transfers_index');
        }

        return $this->render('eggs_inputs_transfers/edit.html.twig', [
            'eggs_inputs_transfer' => $eggsInputsTransfer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_transfers_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggsInputsTransfers $eggsInputsTransfer): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInputsTransfer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsTransfer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_transfers_index');
    }
}
