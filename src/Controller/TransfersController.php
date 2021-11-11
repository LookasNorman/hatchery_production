<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\Transfers;
use App\Form\Production\TransfersProductionType;
use App\Form\TransfersType;
use App\Repository\InputsRepository;
use App\Repository\TransfersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/transfers")
 * @IsGranted("ROLE_USER")
 */
class TransfersController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_transfers_index", methods={"GET"})
     */
    public function index(TransfersRepository $eggsInputsTransfersRepository): Response
    {
        return $this->render('eggs_inputs_transfers/index.html.twig', [
            'eggs_inputs_transfers' => $eggsInputsTransfersRepository->findBy([], ['transferDate' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new/{inputs}/{farm}/{herd}", name="eggs_inputs_transfers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function new(
        Inputs     $inputs,
        InputsFarm $farm,
        Herds      $herd,
        Request    $request
    ): Response
    {
        $transfer = new Transfers();
        $form = $this->createForm(TransfersProductionType::class, $transfer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $transfer->setHerd($herd);
            $transfer->setFarm($farm);
            $transfer->setInput($inputs);
            $entityManager->persist($transfer);
            $entityManager->flush();

            return $this->redirectToRoute('production_transfer_farm', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_inputs_transfers/new.html.twig', ['form' => $form->createView(),
            'input' => $inputs,
            'herd' => $herd,
            'farm' => $farm]);
    }

    /**
     * @Route("/transfer", name="no_transfer_index", methods={"GET"})
     */
    public function showNoTransfer(InputsRepository $inputsRepository): Response
    {
        $transfers = $inputsRepository->inputsNoTransfer();
        return $this->render('eggs_inputs/no_transfer.html.twig', [
            'eggs_inputs' => $transfers,
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_transfers_show", methods={"GET"})
     */
    public function show(Transfers $eggsInputsTransfer): Response
    {
        return $this->render('eggs_inputs_transfers/show.html.twig', [
            'eggs_inputs_transfer' => $eggsInputsTransfer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_transfers_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function edit(Request $request, Transfers $eggsInputsTransfer): Response
    {
        $form = $this->createForm(TransfersType::class, $eggsInputsTransfer);
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
    public function delete(Request $request, Transfers $eggsInputsTransfer): Response
    {

        if ($this->isCsrfTokenValid('delete' . $eggsInputsTransfer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsTransfer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_transfers_index');
    }
}
