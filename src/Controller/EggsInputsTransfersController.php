<?php

namespace App\Controller;

use App\Entity\EggsInputsTransfers;
use App\Form\EggsInputsTransfersType;
use App\Repository\EggsInputsTransfersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/eggs_inputs_transfers")
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
     */
    public function new(Request $request): Response
    {
        $eggsInputsTransfer = new EggsInputsTransfers();
        $form = $this->createForm(EggsInputsTransfersType::class, $eggsInputsTransfer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eggsInputsTransfer);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_transfers_index');
        }

        return $this->render('eggs_inputs_transfers/new.html.twig', [
            'eggs_inputs_transfer' => $eggsInputsTransfer,
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
     */
    public function delete(Request $request, EggsInputsTransfers $eggsInputsTransfer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggsInputsTransfer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsTransfer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_transfers_index');
    }
}
