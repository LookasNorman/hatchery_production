<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\Supplier;
use App\Form\DeliveryProductionType;
use App\Form\DeliveryType;
use App\Repository\BreedRepository;
use App\Repository\HerdsRepository;
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
        $herds = $herdsRepository->findBy(['breeder' => $supplier],['name' => 'ASC']);

        return $this->render('eggs_delivery/production/herd.html.twig', [
            'herds' => $herds
        ]);
    }

    /**
     * @Route("/new/{id}", name="production_delivery_new", methods={"GET","POST"})
     */
    public function deliveryNew(Herds $herd, Request $request): Response
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

            return $this->redirectToRoute('production_index');
        }

        return $this->render('eggs_delivery/production/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }
}
