<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\Supplier;
use App\Form\DeliveryProductionType;
use App\Repository\InputsRepository;
use App\Repository\SupplierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionController extends AbstractController
{
    /**
     * @Route("/", name="production_index")
     */
    public function productionSite()
    {
        return $this->render('main_page/production.html.twig');
    }

    /**
     * @Route("/inputs", name="production_inputs_index")
     */
    public function inputsSite(InputsRepository $inputsRepository)
    {
        $date = new \DateTime();
        $date->modify('-22 days');
        $inputs = $inputsRepository->inputsInProduction($date);

        return $this->render('eggs_inputs/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/delivery/supplier", name="production_delivery_supplier")
     */
    public function deliverySupplier(SupplierRepository $supplierRepository)
    {
        $suppliers = $supplierRepository->findBy([], ['name' => 'ASC']);

        return $this->render('eggs_delivery/production/supplier.html.twig', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * @Route("/delivery/herds/{id}", name="production_delivery_herd")
     */
    public function deliveryHerd(Supplier $supplier)
    {
        $herdsRepository = $this->getDoctrine()->getRepository(Herds::class);
        $herds = $herdsRepository->findBy(['breeder' => $supplier], ['name' => 'ASC']);

        return $this->render('eggs_delivery/production/herd.html.twig', [
            'herds' => $herds
        ]);
    }

    /**
     * @Route("/new/{id}", name="production_delivery_new", methods={"GET","POST"})
     */
    public function deliveryNew(Herds $herd, Request $request, \Swift_Mailer $mailer): Response
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

            $message = (new \Swift_Message('Przyjęto jaja'))
                ->setFrom('lookassymfony@gmail.com')
                ->setTo('lookasziebice@gmail.com')
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/deliveryEgg.html.twig',
                        ['eggs_delivery' => $eggsDelivery]
                    ),
                    'text/html'
                );

            $mailer->send($message);



            return $this->redirectToRoute('production_index');
        }

        return $this->render('eggs_delivery/production/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }
}
