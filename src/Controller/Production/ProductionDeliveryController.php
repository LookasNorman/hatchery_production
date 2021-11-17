<?php

namespace App\Controller\Production;

use App\Entity\ContactInfo;
use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\Supplier;
use App\Form\DeliveryProductionType;
use App\Repository\SupplierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */

class ProductionDeliveryController extends AbstractController
{
    /**
     * @Route("/delivery/supplier", name="production_delivery_supplier")
     */
    public function deliverySupplier(SupplierRepository $supplierRepository): Response
    {
        $suppliers = $supplierRepository->findBy([], ['name' => 'ASC']);

        return $this->render('eggs_delivery/production/supplier.html.twig', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * @Route("/delivery/herds/{id}", name="production_delivery_herd")
     */
    public function deliveryHerd(Supplier $supplier): Response
    {
        $herdsRepository = $this->getDoctrine()->getRepository(Herds::class);
        $herds = $herdsRepository->findBy(['breeder' => $supplier, 'active' => true], ['name' => 'ASC']);

        return $this->render('eggs_delivery/production/herd.html.twig', [
            'herds' => $herds
        ]);
    }

    /**
     * @Route("/new/{id}", name="production_delivery_new", methods={"GET","POST"})
     */
    public function deliveryNew(Herds $herd, Request $request, MailerInterface $mailer): Response
    {
        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        $firstLaying = new \DateTime($deliveryRepository->lastHerdDelivery($herd));
        $eggsDelivery = new Delivery();
        $today = new \DateTime();
        $eggsDelivery->setDeliveryDate($today);
        $eggsDelivery->setLastLayingDate($today);
        $eggsDelivery->setFirstLayingDate($firstLaying);
        $eggsDelivery->setHerd($herd);
        $form = $this->createForm(DeliveryProductionType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $eggsDelivery->setEggsOnWarehouse($eggsDelivery->getEggsNumber());
            $entityManager->persist($eggsDelivery);
            $entityManager->flush();

            $email = $this->sendEmail($eggsDelivery);
            if ($email) {
                $mailer->send($email);
            }

            return $this->redirectToRoute('production_index');
        }

        return $this->render('eggs_delivery/production/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    public function sendEmail($eggsDelivery): TemplatedEmail
    {
        $contactInfoRepository = $this->getDoctrine()->getRepository(ContactInfo::class);
        $hatchery = $contactInfoRepository->findOneBy(['department' => 'Wylęgarnia']);
        $sales = $contactInfoRepository->findOneBy(['department' => 'Handel']);
        $accounting = $contactInfoRepository->findOneBy(['department' => 'Księgowość']);
        $emailAddress = $eggsDelivery->getHerd()->getBreeder()->getEmail();
        $date = $eggsDelivery->getDeliveryDate();

        $email = (new TemplatedEmail())
            ->to('rgolec@zwdmalec.pl')
            ->addTo('kkrakowiak@zwdmalec.pl')
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

}
