<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Entity\Transfers;
use App\Entity\Supplier;
use App\Form\TransfersType;
use App\Repository\DetailsRepository;
use App\Repository\InputsRepository;
use App\Repository\TransfersRepository;
use App\Repository\SupplierRepository;
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
            'eggs_inputs_transfers' => $eggsInputsTransfersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{inputs}/{breeder}", name="eggs_inputs_transfers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new($inputs,
                        $breeder,
                        Request $request,
                        DetailsRepository $detailsRepository,
                        InputsRepository $eggsInputsRepository,
                        SupplierRepository $eggSupplierRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $breeder = $eggSupplierRepository->find($breeder);
        $form = $this->createForm(TransfersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalEggs = 0;
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
            $totalWaste = 0;
            $length = count($inputsDetails) - 1;

            /** @var  $inputDetail
             * Added eggs transfer to eggs input details
             */
            foreach ($inputsDetails as $key => $inputDetail) {
                $eggsInputsTransfer = new Transfers();
                $eggsInputsTransfer->setEggsInputsDetail($inputDetail[0]);
                if ($key < $length) {
                    $setWaste = round($inputDetail[1] / $totalEggs * $wasteEggs, 0);
                    $eggsInputsTransfer->setWasteEggs($setWaste);
                    $totalWaste = $totalWaste + $setWaste;
                } else {
                    $setWaste = $wasteEggs - $totalWaste;
                    $eggsInputsTransfer->setWasteEggs($setWaste);
                }
                $eggsInputsTransfer->setTransferDate($lightingDate);
                $entityManager->persist($eggsInputsTransfer);
            }

            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_inputs_transfers/new.html.twig', [
            'form' => $form->createView(),
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
     * @IsGranted("ROLE_ADMIN")
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
