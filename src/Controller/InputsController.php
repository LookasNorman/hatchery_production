<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\InputsFarmDelivery;
use App\Entity\Lighting;
use App\Entity\Selections;
use App\Entity\Transfers;
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

    public function herdInputFarmDelivery($herd, $input)
    {
        $inputFarmDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
        $inputFarmDeliveries = $inputFarmDeliveryRepository->herdInputFarmDelivery($herd, $input);

        return $inputFarmDeliveries;

    }

    public function herdLightingInInput($herd, $input)
    {
        $lightingRepository = $this->getDoctrine()->getRepository(Lighting::class);
        $lighting = $lightingRepository->herdLightingInInput($herd, $input);
        return $lighting;
    }

    public function herdTransferInInput($herd, $input)
    {
        $transferRepository = $this->getDoctrine()->getRepository(Transfers::class);
        $transfer = $transferRepository->herdTransferInInput($herd, $input);
        return $transfer;
    }

    public function herdSelectionInInput($herd, $input)
    {
        $selectionRepository = $this->getDoctrine()->getRepository(Selections::class);
        $selection = $selectionRepository->herdSelectionInInput($herd, $input);
        return $selection;
    }

    public function herdInInput($input)
    {
        $herdRepository = $this->getDoctrine()->getRepository(Herds::class);
        $herds = $herdRepository->herdInInput($input);

        return $herds;
    }

    public function herdInputEggsInInput($herd, $input)
    {
        $inputFarmDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
        $inputEggs = $inputFarmDeliveryRepository->herdInputEggsInInput($herd, $input);

        return $inputEggs;
    }

    public function herdInInputData($input)
    {
        $herds = $this->herdInInput($input);
        $herdData = [];
        foreach ($herds as $herd) {
            $inputEggs = $this->herdInputEggsInInput($herd, $input);
            $lighting = $this->herdLightingInInput($herd, $input);
            $transfer = $this->herdTransferInInput($herd, $input);
            $selection = $this->herdSelectionInInput($herd, $input);
            array_push($herdData, [
                'herd' => $herd,
                'inputEggs' => $inputEggs,
                'eggsNumber' => 5,
                'lighting' => $lighting,
                'transfer' => $transfer,
                'selection' => $selection
            ]);
        }
        return $herdData;
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
        $herds = $this->herdInInput($eggsInput);
        $herdsData = $this->herdInInputData($eggsInput);

        return $this->render('eggs_inputs/show.html.twig', [
            'eggs_input' => $eggsInput,
            'farms' => $farms,
            'herdsEggs' => $herdsData,
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
