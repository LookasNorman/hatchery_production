<?php

namespace App\Controller;

use App\Entity\ContactInfo;
use App\Entity\TransportInputsFarm;
use App\Entity\TransportList;
use App\Form\TransportListEditType;
use App\Form\TransportListType;
use App\Repository\TransportListRepository;
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
     * @Route("/", name="transport_list_index", methods={"GET"})
     */
    public function index(TransportListRepository $transportListRepository): Response
    {
        return $this->render('transport_list/index.html.twig', [
            'transport_lists' => $transportListRepository->findAll(),
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
        foreach ($farms as $key => $farm){
            if($key != 0){
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
            'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origins. '&destinations= ' . $destination . '&departure_time=now&key=' . $apiKey);
        $content = $response->toArray();
        return $content;
    }

    public function googleDirectionsMatrix($farms)
    {
        $apiKey = $this->getParameter('app.mapsfullkey');
        $destination = null;
        $origins = '05-530+Dębówka+1a';
        foreach ($farms as $key => $farm){
            if($key != 0){
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
     * @Route("/new", name="transport_list_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TRANSPORT")
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $transportList = new TransportList();
        $form = $this->createForm(TransportListType::class, $transportList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $distanceMatrix = $this->googleDistanceMatrix($transportList->getFarm());
            $directionsMatrix = $this->googleDirectionsMatrix($transportList->getFarm());
            $transportListDistance = 0;
            foreach ($directionsMatrix['routes'][0]['legs'] as $leg){
                $transportListDistance = $transportListDistance + round($leg['distance']['value'] / 1000, 0);
            }
            $transportList->setDistance($transportListDistance);

            $entityManager->persist($transportList);
            foreach ($transportList->getFarm() as $key => $transport) {
                $distanceMatrixPoint = $distanceMatrix['rows'][0]['elements'][$key];
                $distanceFromHatchery = round($distanceMatrixPoint['distance']['value'] / 1000, 0);
                if($key === 0){
                    $arrivalTime = clone $transportList->getDepartureHour();
                }

                $time = $directionsMatrix['routes'][0]['legs'][$key]['duration']['value'];
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
}
