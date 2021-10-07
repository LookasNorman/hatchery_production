<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Form\InputsType;
use App\Repository\DeliveryRepository;
use App\Repository\DetailsDeliveryRepository;
use App\Repository\DetailsRepository;
use App\Repository\HerdsRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\InputsFarmRepository;
use App\Repository\InputsRepository;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/inputs")
 * @IsGranted("ROLE_USER")
 */
class InputsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_index", methods={"GET"})
     */
    public function index(InputsRepository $eggsInputsRepository): Response
    {
        return $this->render('eggs_inputs/index.html.twig', [
            'eggs_inputs' => $eggsInputsRepository->findBy([], ['inputDate' => 'desc']),
        ]);
    }

    /**
     * @Route("/new", name="eggs_inputs_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsInput = new Inputs();
        $form = $this->createForm(InputsType::class, $eggsInput);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eggsInput);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_index');
        }

        return $this->render('eggs_inputs/new.html.twig', [
            'eggs_input' => $eggsInput,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_show", methods={"GET"})
     */
    public function show(
        Inputs                       $eggsInput,
        InputsFarmRepository         $farmRepository,
        InputsFarmDeliveryRepository $inputsFarmDeliveryRepository
    ): Response
    {
        $farms = $farmRepository->findBy(['eggInput' => $eggsInput]);

        $herdsEggs = [];
        $herds = [];
        foreach ($farms as $farm) {
            $farmsDelivery = $inputsFarmDeliveryRepository->findBy(['inputsFarm' => $farm]);
            foreach ($farmsDelivery as $farmDelivery) {
                $herdDelivery = [];
                if (!in_array($farmDelivery->getDelivery()->getHerd(), $herds, true)) {
                    $herd = $farmDelivery->getDelivery()->getHerd();
                    $eggsNumber = 0;
                    $lightingsData = [];
                    $transfersData = [];
                    $selectionsData = [];
                    foreach ($farmsDelivery as $farmDelivery) {
                        if ($farmDelivery->getDelivery()->getHerd() === $herd) {
                            array_push($herdDelivery, $farmDelivery->getDelivery());
                            $eggsNumber += $farmDelivery->getEggsNumber();
                            $lightings = $farmDelivery->getLighting();
                            $transfers = $farmDelivery->getTransfers();
                            $selections = $farmDelivery->getSelections();
                            array_push($lightingsData, $lightings);
                            array_push($transfersData, $transfers);
                            array_push($selectionsData, $selections);
                        }
                    }

                    $eggsLighting = 0;
                    $eggsWasteLighting = 0;
                    $wasteLighting = 0;

                    foreach ($lightingsData as $lighting) {
                        if(is_object($lighting)){
                            $lightingDate = $lighting->getLightingDate();
                            $eggsLighting = $eggsLighting + $lighting->getLightingEggs();
                            $eggsWasteLighting = $eggsWasteLighting + $lighting->getWasteEggs();
                            $wasteLighting = $wasteLighting + $lighting->getWasteLighting();
                        } else {
                            $lightingDate = null;
                            $eggsLighting = null;
                            $eggsWasteLighting = null;
                            $wasteLighting = null;
                        }
                    }
                    if($eggsLighting > 0){
                        $eggsAllWasteLighting = round($eggsNumber / $eggsLighting * $eggsWasteLighting, 0);
                        $fertilization = round((1 - ($eggsWasteLighting / $eggsLighting)) * 100, 1);
                        $herdFertilization = round((1 - ($wasteLighting / $eggsNumber)) * 100, 1);
                    } else {
                        $eggsAllWasteLighting = null;
                        $fertilization = null;
                        $herdFertilization = null;
                    }
                    $lightings = [
                        'lightingDate' => $lightingDate,
                        'eggsLighting' => $eggsLighting,
                        'eggsWasteLighting' => $eggsWasteLighting,
                        'eggsAllWasteLighting' => $eggsAllWasteLighting,
                        'wasteLighting' => $wasteLighting,
                        'fertilization' => $fertilization,
                        'herdFertilization' => $herdFertilization
                    ];

                    $eggsTransfers = 0;

                    foreach ($transfersData as $transfer) {
                        if(is_object($transfer)){
                            $transferDate = $transfer->getTransferDate();
                            $eggsTransfers = $eggsTransfers + $transfer->getTransfersEgg();
                        } else {
                            $transferDate = null;
                            $eggsTransfers = null;
                        }
                    }
                    $transfers = [
                        'transferDate' => $transferDate,
                        'eggsTransfer' => $eggsTransfers
                    ];

                    $selectionDate = null;
                    $chicks = null;
                    $cullChicks = null;
                    $unhatched = null;

                    foreach ($selectionsData as $selection) {
                        if(is_object($selection)){
                            $selectionDate = $selection->getSelectionDate();
                            $chicks = $chicks + $selection->getChickNumber();
                            $cullChicks = $cullChicks + $selection->getCullChicken();
                            $unhatched = $unhatched + $selection->getUnhatched();
                        }
                    }
                    $selections = [
                        'selectionDate' => $selectionDate,
                        'chicks' => $chicks,
                        'cullChicks' => $cullChicks,
                        'unhatched' => $unhatched
                    ];


                    array_push($herdsEggs,
                        [
                            'herd' => $herd,
                            'eggsNumber' => $eggsNumber,
                            'lighting' => $lightings,
                            'transfer' => $transfers,
                            'selection' => $selections
                        ]);
                    array_push($herds, $herd);
                }
            }
        }
        return $this->render('eggs_inputs/show.html.twig', [
            'eggs_input' => $eggsInput,
            'farms' => $farms,
            'herdsEggs' => $herdsEggs,
            'herds' => $herds
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Inputs $eggsInput): Response
    {
        $form = $this->createForm(InputsType::class, $eggsInput);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_index');
        }

        return $this->render('eggs_inputs/edit.html.twig', [
            'eggs_input' => $eggsInput,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Inputs $eggsInput): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInput->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInput);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_index');
    }
}
