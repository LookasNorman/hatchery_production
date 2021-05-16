<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Form\InputsType;
use App\Repository\DetailsDeliveryRepository;
use App\Repository\DetailsRepository;
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
     * @Route("/pdf", name="eggs_inputs_pdf")
     */
    public function pdf(InputsRepository $eggsInputsRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('eggs_inputs/index.html.twig', [
            'eggs_inputs' => $eggsInputsRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @Route("/xls/{id}", name="eggs_inputs_xls")
     */
    public function xls(Inputs $eggsInput, DetailsRepository $detailsRepository): StreamedResponse
    {
        $details = $detailsRepository->deliveriesInput($eggsInput);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getHeaderFooter()
            ->setOddHeader('Zarządzanie produkcją');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($eggsInput->getName());
        $spreadsheet->getProperties()->setTitle($eggsInput->getName());
        $spreadsheet->getActiveSheet()->getHeaderFooter()
            ->setOddFooter('&Lhttps://lookaskonieczny.com' . '&C' . $spreadsheet->getProperties()->getTitle() . '&RPage &P of &N');


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
                        number_format($chickNumber, 0, ',', ' '),
                        'KL',
                        $breederName,
                        $herdName,
                        $deliveryDate->format('Y-m-d'),
                        'OD',
                        $age,
                        number_format($eggsNumber, 0, ',', ' '),
                        number_format($wasteLightingsEggs, 0, ',', ' '),
                        number_format($lightingFertilization, 2, ',', ' '),
                        number_format($wasteTransferEggs, 0, ',', ' '),
                        number_format($transfersFertilization, 2, ',', ' '),
                        number_format($chicksSelections, 0, ',', ' '),
                        number_format($cullChick, 0, ',', ' '),
                        number_format($wasteSelectionsEggs, 0, ',', ' '),
                        number_format($hatchability, 2, ',', ' '),
                        number_format($hatchabilityFertilized, 2, ',', ' '),
                        number_format($cullPercent, 2, ',', ' '),
                        number_format($diff, 2, ',', ' '),
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
                        number_format($eggsNumber, 0, ',', ' '),
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
        $rowsNumber = count($data) + 1;
        $sheet->fromArray($data, null, 'A2', true);
        $spreadsheet->getActiveSheet()->getStyle('A1:U' . $rowsNumber)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:U' . $rowsNumber)
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('A1:U' . $rowsNumber)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'startColor' => [
                    'rgb' => 'EEEEEE'
                ]
            ],
        ];
        $styleBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:U' . $rowsNumber)->applyFromArray($styleBorders);
        $spreadsheet->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        $columnsDimensions = [
            'A' => 'auto',
            'B' => 25,
            'C' => 20,
            'D' => 'auto',
            'E' => 20,
            'F' => 'auto',
            'G' => 22,
            'H' => 20,
            'I' => 29,
            'J' => 29,
            'K' => 34,
            'L' => 29,
            'M' => 34,
            'N' => 25,
            'O' => 29,
            'P' => 34,
            'Q' => 20,
            'R' => 36,
            'S' => 29,
            'T' => 20,
            'U' => 29
        ];
        foreach ($columnsDimensions as $key => $column) {
            if ($column == 'auto') {
                $spreadsheet->getActiveSheet()->getColumnDimension($key)->setAutoSize(true);
            } else {
                $spreadsheet->getActiveSheet()->getColumnDimension($key)->setWidth($column / 2.5);
            }
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $filename = $eggsInput->getName() . '.xls';
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }

    /**
     * @Route("/{id}", name="eggs_inputs_show", methods={"GET"})
     */
    public function show(Inputs $eggsInput, DetailsRepository $detailsRepository, DetailsDeliveryRepository $deliveryRepository): Response
    {
        $inputDetails = $detailsRepository->deliveriesInput($eggsInput);
        $details = [];
        $breeders = [];

        foreach ($inputDetails as $detail) {
            $eggs = 0;
            $wasteLighting = 0;
            $wasteTransfer = 0;
            $deliveries = $deliveryRepository->findBy(['eggsInputDetails' => $detail]);
            foreach ($deliveries as $delivery) {
                $eggs = $eggs + $delivery->getEggsNumber();
                $breeders [$delivery->getEggsDeliveries()->getHerd()->getBreeder()->getId()] = ($delivery->getEggsDeliveries()->getHerd()->getBreeder());
            }
            $detail->eggsNumber = $eggs;
            foreach ($detail->getEggsInputsLightings() as $lighting) {
                $wasteLighting = $wasteLighting + $lighting->getWasteEggs();
            }
            if ($wasteLighting > 0) {
                $detail->fertilization = ($eggs - $wasteLighting) / $eggs * 100;
            } else {
                $detail->fertilization = null;
            }
            foreach ($detail->getEggsInputsTransfers() as $transfers) {
                $wasteTransfer = $wasteTransfer + $transfers->getWasteEggs();
            }
            if ($wasteTransfer > 0) {
                $detail->fertilizationTransfer = ($eggs - $wasteLighting - $wasteTransfer) / $eggs * 100;
            } else {
                $detail->fertilizationTransfer = null;
            }
            $chickNumber = 0;
            $cullChick = 0;
            foreach ($detail->getEggsSelections() as $selections) {
                $chickNumber = $chickNumber + $selections->getChickNumber();
                $cullChick = $cullChick + $selections->getCullChicken();
            }
            if ($chickNumber > 0) {
                $detail->hatchability = $chickNumber / $eggs * 100;
                $detail->cullChick = $cullChick / $eggs * 100;
            } else {
                $detail->hatchability = null;
                $detail->cullChick = null;
            }
            if ($cullChick > 0 or $chickNumber > 0) {
                $unhatched = $eggs - $wasteLighting - $wasteTransfer - $cullChick - $chickNumber;
                if ($unhatched <> $eggs) {
                    $detail->unhatched = $unhatched;
                } else {
                    $detail->unhatched = null;
                }
            } else {
                $detail->unhatched = null;
            }
            $details [] = $detail;
        }
        return $this->render('eggs_inputs/show.html.twig', [
            'eggs_input' => $eggsInput,
            'details_input' => $details,
            'breeders' => $breeders,
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
