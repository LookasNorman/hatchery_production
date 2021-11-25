<?php

namespace App\Controller;

use App\Entity\ContactInfo;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\TransportInputsFarm;
use App\Entity\TransportList;
use App\Form\BetweenDateType;
use App\Form\ChooseInputType;
use App\Form\TransportListEditType;
use App\Form\TransportListType;
use App\Repository\InputsRepository;
use App\Repository\TransportInputsFarmRepository;
use App\Repository\TransportListRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transport_list")
 * @IsGranted("ROLE_USER")
 */
class TransportListController extends AbstractController
{
    /**
     * @Route("/", name="transport_list_index", methods={"GET", "POST"})
     */
    public function index(Request $request, TransportListRepository $transportListRepository): Response
    {
        $form = $this->createForm(BetweenDateType::class);
        $form->handleRequest($request);
        $week = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $date = $form->get('startDate')->getData();
            $start = clone $date;
            $start->modify('-21 days');
            $week = $date->format('W');
            $end = $form->get('endDate')->getData()->modify('-20 days');
            return $this->render('transport_list/index.html.twig', [
                'transport_lists' => $transportListRepository->transportList($start, $end),
                'form' => $form->createView(),
                'week' => $week
            ]);
        }

        return $this->render('transport_list/index.html.twig', [
            'transport_lists' => $transportListRepository->findAll(),
            'form' => $form->createView(),
            'week' => $week
        ]);
    }

    public function sendEmail($farm, $driver, $arrivalHour, $car)
    {
        $contactInfoRepository = $this->getDoctrine()->getRepository(ContactInfo::class);
        $hatchery = $contactInfoRepository->findOneBy(['department' => 'Wylęgarnia']);
        $transport = $contactInfoRepository->findOneBy(['department' => 'Transport']);
        $accounting = $contactInfoRepository->findOneBy(['department' => 'Księgowość']);
        $date = clone $farm->getEggInput()->getInputDate();
        $date->modify('+ 21 days');

        $email = (new TemplatedEmail())
            ->to($transport->getEmail())
            ->addBcc('lkonieczny@zwdmalec.pl')
            ->subject('Planowana dostawa piskląt ' . $date->format('Y-m-d'))
            ->htmlTemplate('emails/transportList.html.twig')
            ->context([
                'farm' => $farm,
                'driver' => $driver,
                'arrivalHour' => $arrivalHour,
                'car' => $car,
                'hatchery' => $hatchery,
                'transport' => $transport,
                'accounting' => $accounting
            ]);
//        if ($emailAddress) {
//            $email->addTo($emailAddress);
//        }

        return $email;
    }

    public function googleDistanceMatrix($farms)
    {
        $apiKey = $this->getParameter('app.mapsfullkey');
        $destination = null;
        $origins = '05-530+Dębówka+1a';
        foreach ($farms as $key => $farm) {
            if ($key != 0) {
                $destination .= '%7C';
            }
            $destination .= $farm->getChicksFarm()->getPostcode();
            $destination .= '+';
            $destination .= $farm->getChicksFarm()->getCity();
            $destination .= '+';
            $destination .= $farm->getChicksFarm()->getStreet();
            $destination .= '+';
            $destination .= $farm->getChicksFarm()->getStreetNumber();
        }
        $client = HttpClient::create();
        $response = $client->request('GET',
            'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origins . '&destinations= ' . $destination . '&departure_time=now&key=' . $apiKey);
        $content = $response->toArray();
        return $content;
    }

    public function googleDirectionsMatrix($farms)
    {
        $apiKey = $this->getParameter('app.mapsfullkey');
        $destination = null;
        $origins = '05-530+Dębówka+1a';
        foreach ($farms as $key => $farm) {
            if ($key != 0) {
                $destination .= '%7C';
            }
            $destination .= $farm->getChicksFarm()->getPostcode();
            $destination .= '+';
            $destination .= $farm->getChicksFarm()->getCity();
            $destination .= '+';
            $destination .= $farm->getChicksFarm()->getStreet();
            $destination .= '+';
            $destination .= $farm->getChicksFarm()->getStreetNumber();
        }
        $client = HttpClient::create();
        $response = $client->request('GET',
            'https://maps.googleapis.com/maps/api/directions/json?origin=' . $origins .
            '&destination=' . $origins .
            '&waypoints=' . $destination .
            '&key=' . $apiKey);
        $content = $response->toArray();
        return $content;
    }

    /**
     * @Route("/choose_input", name="transport_list_choose_input", methods={"GET","POST"})
     * @IsGranted("ROLE_TRANSPORT")
     */
    public function chooseInput(Request $request, InputsRepository $inputsRepository)
    {
        $form = $this->createForm(ChooseInputType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('transport_list_new', [
                'input' => $form->get('input')->getData()->getId()
            ]);
        }
        return $this->render('transport_list/input_choose.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new/{input}", name="transport_list_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TRANSPORT")
     */
    public function new(Inputs $input, Request $request, MailerInterface $mailer): Response
    {
        $transportList = new TransportList();
        $form = $this->createForm(TransportListType::class, $transportList, [
            'input' => $input
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $distanceMatrix = $this->googleDistanceMatrix($transportList->getFarm());
            $directionsMatrix = $this->googleDirectionsMatrix($transportList->getFarm());
            $transportListDistance = 0;
            if ($directionsMatrix['status'] != 'OK') {
                return $this->render('transport_list/no_address.html.twig', [
                    'farms' => $transportList->getFarm()
                ]);
            }

            foreach ($directionsMatrix['routes'][0]['legs'] as $leg) {
                $transportListDistance = $transportListDistance + round($leg['distance']['value'] / 1000, 0);
            }
            $transportList->setDistance($transportListDistance);

            $entityManager->persist($transportList);
            foreach ($transportList->getFarm() as $key => $transport) {
                $distanceMatrixPoint = $distanceMatrix['rows'][0]['elements'][$key];
                $distanceFromHatchery = round($distanceMatrixPoint['distance']['value'] / 1000, 0);
                if ($key === 0) {
                    $arrivalTime = clone $transportList->getDepartureHour();
                }

                $time = $directionsMatrix['routes'][0]['legs'][$key]['duration']['value'] * 1.4;
                $distance = round($directionsMatrix['routes'][0]['legs'][$key]['distance']['value'] / 1000, 0);
                $arrivalTime->modify('+' . $time . ' seconds');
                $time = clone $arrivalTime;
                $transportInputFarm = new TransportInputsFarm();
                $transportInputFarm->setFarm($transport);
                $transportInputFarm->setTransportList($transportList);

                $transportInputFarm->setArrivalTime($time);
                $transportInputFarm->setDistance($distance);
                $transportInputFarm->setDistanceFromHatchery($distanceFromHatchery);
                $entityManager->persist($transportInputFarm);
                $email = $this->sendEmail($transport, $transportList->getDriver(), $time, $transportList->getCar());
                $arrivalTime->modify('+1 hour');
                if ($email) {
                    $mailer->send($email);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('transport_list_index');
        }

        return $this->render('transport_list/new.html.twig', [
            'transport_list' => $transportList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_list_show", methods={"GET"})
     */
    public function show(TransportList $transportList): Response
    {
        return $this->render('transport_list/show.html.twig', [
            'transport_list' => $transportList,
            'maps_api' => $this->getParameter('app.mapskey')
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transport_list_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_TRANSPORT")
     */
    public function edit(Request $request, TransportList $transportList): Response
    {
        $form = $this->createForm(TransportListEditType::class, $transportList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transport_list_index');
        }

        return $this->render('transport_list/edit.html.twig', [
            'transport_list' => $transportList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_list_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, TransportList $transportList): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transportList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transportList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transport_list_index');
    }

    /**
     * @Route("/protocol_pdf/{farm}", name="transport_list_protocol", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function transportProtocolPdf(InputsFarm $farm, Pdf $pdf, TransportInputsFarmRepository $transportRepository)
    {
        $transport = $transportRepository->findOneBy(['farm' => $farm]);
        $html = $this->renderView('pdf/protocol.html.twig', [
            'farm' => $farm,
            'transport' => $transport,
        ]);
        $filename = $farm->getChicksFarm()->getCustomer()->getName()
            . ' - ' . $farm->getChicksFarm()->getName()
            . ' - ' . $farm->getEggInput()->getInputDate()->modify('+21 day')->format('Y-m-d')
            . '.pdf';
        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            $filename
        );
    }
}
