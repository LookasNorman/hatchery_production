<?php

namespace App\Controller;

use App\Entity\EggsInputs;
use App\Form\EggsInputsType;
use App\Repository\EggsInputsDetailsEggsDeliveryRepository;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsRepository;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_inputs")
 * @IsGranted("ROLE_USER")
 */
class EggsInputsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_index", methods={"GET"})
     */
    public function index(EggsInputsRepository $eggsInputsRepository): Response
    {
        return $this->render('eggs_inputs/index.html.twig', [
            'eggs_inputs' => $eggsInputsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_inputs_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsInput = new EggsInputs();
        $form = $this->createForm(EggsInputsType::class, $eggsInput);
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
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @Route("/xls/{id}", name="eggs_inputs_xls")
     */
    public function xls(EggsInputs $eggsInput, EggsInputsDetailsRepository $detailsRepository): StreamedResponse
    {
        $details = $detailsRepository->deliveriesInput($eggsInput);

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle($eggsInput->getName());

        $sheet->getCell('A1')->setValue('Odbiorca piskląt');
        $sheet->getCell('B1')->setValue('ilość piskląt');
        $sheet->getCell('C1')->setValue('Aparaty lęgowe');
        $sheet->getCell('D1')->setValue('Dostawca jaj');
        $sheet->getCell('E1')->setValue('Stado');
        $sheet->getCell('F1')->setValue('Data dostawy');
        $sheet->getCell('G1')->setValue('Odpad z dostawy');
        $sheet->getCell('H1')->setValue('Wiek stada');
        $sheet->getCell('I1')->setValue('Liczba nałożonych jaj');
        $sheet->getCell('J1')->setValue('Odpad po świetleniu');
        $sheet->getCell('K1')->setValue('% zapłodnienia');
        $sheet->getCell('L1')->setValue('Odpad po przekładzie');
        $sheet->getCell('M1')->setValue('% zapłodnienia');
        $sheet->getCell('N1')->setValue('Wylęg');
        $sheet->getCell('O1')->setValue('Brakowanie');
        $sheet->getCell('P1')->setValue('Liczba jaj niewyklutych');
        $sheet->getCell('Q1')->setValue('% wylęgu');
        $sheet->getCell('R1')->setValue('% wylęg z jaj zapłodnionych');
        $sheet->getCell('S1')->setValue('% brakowania');
        $sheet->getCell('T1')->setValue('różnica między % zapł. i % wylęgu');
        $sheet->getCell('U1')->setValue('Aparaty klujnikowe');
        $data = [];
        foreach ($details as $detail) {
            foreach ($detail->getEggsInputsDetailsEggsDeliveries() as $key => $delivery) {
                $wasteLightingsEggs = null;
                $wasteTransferEggs = null;
                $chicksSelections = null;
                $cullChick = null;

                if (!empty($detail->getEggsInputsLightings()[0])) {
                    $wasteLightingsEggs = $detail->getEggsInputsLightings()[0]->getWasteEggs();
                }

                if (!empty($detail->getEggsInputsLightings()[0])) {
                    $wasteTransferEggs = $detail->getEggsInputsTransfers()[0]->getWasteEggs();
                }

                if (!empty($detail->getEggsSelections()[0])) {
                    $chicksSelections = $detail->getEggsSelections()[0]->getChickNumber();
                    $cullChick = $detail->getEggsSelections()[0]->getCullChicken();
                }
                $recipientName = $detail->getChicksRecipient()->getName();
                $chickNumber = $detail->getChickNumber();
                $breederName = $delivery->getEggsDeliveries()->getHerd()->getBreeder()->getName();
                $herdName = $delivery->getEggsDeliveries()->getHerd()->getName();
                $hatchingDate = $delivery->getEggsDeliveries()->getHerd()->getHatchingDate();
                $eggsNumber = $delivery->getEggsNumber();
                $deliveryDate = $delivery->getEggsDeliveries()->getDeliveryDate();
                $age = (int)($hatchingDate->diff($deliveryDate)->days / 7);
                $lightingFertilization = ($eggsNumber - $wasteLightingsEggs) / $eggsNumber * 100;
                $transfersFertilization = ($eggsNumber - ($wasteLightingsEggs + $wasteTransferEggs)) / $eggsNumber * 100;
                $wasteSelectionsEggs = $eggsNumber - ($wasteLightingsEggs + $wasteTransferEggs + $chicksSelections + $cullChick);
                $hatchability = $chicksSelections / $eggsNumber * 100;
                $hatchabilityFertilized = $chicksSelections / ($eggsNumber - ($wasteLightingsEggs + $wasteTransferEggs)) * 100;
                $cullPercent = $cullChick / $eggsNumber * 100;
                $diff = $transfersFertilization - $hatchability;

                if ($key == 0) {
                    $data [] = [
                        $recipientName,
                        $chickNumber,
                        'KL',
                        $breederName,
                        $herdName,
                        $deliveryDate->format('Y-m-d'),
                        'OD',
                        $age,
                        $eggsNumber,
                        $wasteLightingsEggs,
                        $lightingFertilization,
                        $wasteTransferEggs,
                        $transfersFertilization,
                        $chicksSelections,
                        $cullChick,
                        $wasteSelectionsEggs,
                        $hatchability,
                        $hatchabilityFertilized,
                        $cullPercent,
                        $diff,
                        'KK'
                    ];
                } else {
                    $data [] = [
                        '',
                        '',
                        'KL',
                        $breederName,
                        $herdName,
                        $deliveryDate->format('Y-m-d'),
                        'OD',
                        $age,
                        $eggsNumber,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'KK'
                    ];
                }

            }
        }
        $sheet->fromArray($data, null, 'A2', true);


        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $filename = $eggsInput->getName() . '.xls';
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename='. $filename);
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }

    /**
     * @Route("/{id}", name="eggs_inputs_show", methods={"GET"})
     */
    public function show(EggsInputs $eggsInput, EggsInputsDetailsRepository $detailsRepository, EggsInputsDetailsEggsDeliveryRepository $deliveryRepository): Response
    {
        $inputDetails = $detailsRepository->deliveriesInput($eggsInput);
        foreach ($inputDetails as $detail){
            $eggs = 0;
            $wasteLighting = 0;
            $wasteTransfer = 0;
            $deliveries = $deliveryRepository->findBy(['eggsInputDetails' => $detail]);
            foreach ($deliveries as $delivery){
                $eggs = $eggs + $delivery->getEggsNumber();
            }
            $detail->eggsNumber = $eggs;
            foreach ($detail->getEggsInputsLightings() as $lighting){
                $wasteLighting = $wasteLighting + $lighting->getWasteEggs();
            }
            if($wasteLighting > 0){
                $detail->fertilization = ($eggs - $wasteLighting) / $eggs * 100;
            } else {
                $detail->fertilization = null;
            }
            foreach ($detail->getEggsInputsTransfers() as $transfers){
                $wasteTransfer = $wasteTransfer + $transfers->getWasteEggs();
            }
            if($wasteTransfer > 0){
                $detail->fertilizationTransfer = ($eggs - $wasteLighting - $wasteTransfer) / $eggs * 100;
            } else {
                $detail->fertilizationTransfer = null;
            }
            $chickNumber = 0;
            $cullChick = 0;
            foreach ($detail->getEggsSelections() as $selections){
                $chickNumber = $chickNumber + $selections->getChickNumber();
                $cullChick = $cullChick + $selections->getCullChicken();
            }
            if($chickNumber > 0){
                $detail->hatchability = $chickNumber / $eggs * 100;
                $detail->cullChick = $cullChick / $eggs * 100;
            } else {
                $detail->hatchability = null;
                $detail->cullChick = null;
            }
            $unhatched = $eggs - $wasteLighting - $wasteTransfer - $cullChick - $chickNumber;
            if($unhatched <> $eggs){
                $detail->unhatched = $unhatched;
            } else {
                $detail->unhatched = null;
            }
//            dump($detail);
            $details [] = $detail;
        }
//        die();
        return $this->render('eggs_inputs/show.html.twig', [
            'eggs_input' => $eggsInput,
            'details_input' => $details,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsInputs $eggsInput): Response
    {
        $form = $this->createForm(EggsInputsType::class, $eggsInput);
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
    public function delete(Request $request, EggsInputs $eggsInput): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInput->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInput);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_index');
    }
}
