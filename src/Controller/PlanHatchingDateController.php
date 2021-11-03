<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Repository\HerdsRepository;
use App\Repository\PlanDeliveryEggRepository;
use App\Repository\SupplierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plans_hatching_date")
 * @IsGranted("ROLE_USER")
 */
class PlanHatchingDateController extends AbstractController
{
    /**
     * @Route("/", name="plan_hatching_date")
     */
    public function index(HerdsRepository $herdsRepository)
    {
        $hatchingDates = $herdsRepository->hatchingDate();
        $now = new \DateTime();

        return $this->render('plan_breeder/hatching_date.html.twig', [
            'dates' => $hatchingDates,
            'now' => $now
        ]);
    }

    /**
     * @Route("/{date}", name="plan_hatching_date_day")
     */
    public function indexDay(\DateTime $date, PlanDeliveryEggRepository $deliveryEggRepository, SupplierRepository $supplierRepository): Response
    {
        $plans = $deliveryEggRepository->planHatchingDate($date);
        $suppliers = $supplierRepository->supplierByHatchingDate($date);

        return $this->render('plan_breeder/index.html.twig', [
            'plans' => $plans,
            'date' => $date,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * @Route("/week/{date}", name="plan_hatching_week")
     */
    public function week(\DateTime $date, PlanDeliveryEggRepository $deliveryEggRepository, SupplierRepository $supplierRepository): Response
    {
        $plans = $deliveryEggRepository->planHatchingDateWeek($date);
        $suppliers = $supplierRepository->supplierByHatchingDate($date);

        return $this->render('plan_breeder/week.html.twig', [
            'plans' => $plans,
            'date' => $date,
            'suppliers' => $suppliers
        ]);
    }
}
