<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\ContactInfo;
use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputsFarmDelivery;
use App\Entity\Supplier;
use App\Form\DeliveryPartIndexType;
use App\Form\DeliveryType;
use App\Repository\DeliveryRepository;
use App\Repository\DetailsDeliveryRepository;
use App\Repository\InputsFarmDeliveryRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/delivery")
 * @IsGranted("ROLE_USER")
 */
class DeliveryController extends AbstractController
{
    public function eggsOnWarehouse($deliveries)
    {
        $inputsFarmDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
        $eggsDeliveries = [];
        foreach ($deliveries as $delivery) {
            $inputsDeliveries = $inputsFarmDeliveryRepository->eggsFromDelivery($delivery);
            if ($inputsDeliveries > 0) {
                array_push($eggsDeliveries, ['delivery' => $delivery, 'eggs' => (int)$inputsDeliveries]);
            } elseif (is_null($inputsDeliveries)) {
                array_push($eggsDeliveries, ['delivery' => $delivery, 'eggs' => (int)$delivery->getEggsNumber()]);
            }
        }

        return $eggsDeliveries;
    }

    public function eggsWarehouse($deliveries)
    {
        $inputsFarmDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
        $eggsDeliveries = [];
        foreach ($deliveries as $delivery) {
            $inputsDeliveries = $inputsFarmDeliveryRepository->eggsFromDelivery($delivery);
            if (is_null($inputsDeliveries)) {
                array_push($eggsDeliveries, ['delivery' => $delivery, 'eggs' => (int)$delivery->getEggsNumber()]);
            } else {
                array_push($eggsDeliveries, ['delivery' => $delivery, 'eggs' => (int)$inputsDeliveries]);
            }
        }

        return $eggsDeliveries;
    }

    /**
     * @Route("/", name="eggs_delivery_index", methods={"GET"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function index(DeliveryRepository $eggsDeliveryRepository): Response
    {
        $deliveries = $eggsDeliveryRepository->findBy([], ['deliveryDate' => 'desc']);
        $eggsDeliveries = $this->eggsOnWarehouse($deliveries);

        return $this->render('eggs_delivery/index.html.twig', [
            'eggs_deliveries' => $eggsDeliveries,
        ]);
    }

    /**
     * @Route("/all", name="eggs_delivery_all_index", methods={"GET"})
     */
    public function allDelivery(DeliveryRepository $eggsDeliveryRepository): Response
    {
        $deliveries = $eggsDeliveryRepository->findAll();
        $eggsDeliveries = $this->eggsWarehouse($deliveries);

        return $this->render('eggs_delivery/index.html.twig', [
            'eggs_deliveries' => $eggsDeliveries,
        ]);
    }

    public function sendEmail($eggsDelivery)
    {
        $contactInfoRepository = $this->getDoctrine()->getRepository(ContactInfo::class);
        $hatchery = $contactInfoRepository->findOneBy(['department' => 'Wylęgarnia']);
        $sales = $contactInfoRepository->findOneBy(['department' => 'Handel']);
        $accounting = $contactInfoRepository->findOneBy(['department' => 'Księgowość']);
        $emailAddress = $eggsDelivery->getHerd()->getBreeder()->getEmail();
        $date = $eggsDelivery->getDeliveryDate();

        $email = (new TemplatedEmail())
            ->to('rgolec@zwdmalec.pl')
            ->addBcc('lkonieczny@zwdmalec.pl')
            ->subject('Przyjęcie jaj w dniu ' . $date->format('Y-m-d'))
            ->htmlTemplate('emails/deliveryEgg.html.twig')
            ->context([
                'eggs_delivery' => $eggsDelivery,
                'hatchery' => $hatchery,
                'sales' => $sales,
                'accounting' => $accounting
            ]);
        if ($emailAddress) {
            $email->addTo($emailAddress);
        }
        return $email;
    }

    /**
     * @Route("/new", name="eggs_delivery_new", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $eggsDelivery = new Delivery();
        $form = $this->createForm(DeliveryType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $eggsDelivery->setEggsOnWarehouse($eggsDelivery->getEggsNumber());
            $entityManager->persist($eggsDelivery);
            $email = $this->sendEmail($eggsDelivery);
            if ($email) {
                $mailer->send($email);
            }

            $entityManager->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_delivery_show", methods={"GET"})
     */
    public function show(Delivery $eggsDelivery): Response
    {
        return $this->render('eggs_delivery/show.html.twig', [
            'eggs_delivery' => $eggsDelivery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_delivery_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function edit(Request $request, Delivery $eggsDelivery): Response
    {
        $form = $this->createForm(DeliveryType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/edit.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/add", name="eggs_delivery_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addPartIndex(Request $request, Delivery $eggsDelivery)
    {
        $form = $this->createForm(DeliveryPartIndexType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/addPartIndex.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_delivery_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Delivery $eggsDelivery): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsDelivery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsDelivery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_delivery_index');
    }
}
