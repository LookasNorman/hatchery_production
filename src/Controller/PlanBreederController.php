<?php

namespace App\Controller;

use App\Entity\PlanDeliveryEgg;
use App\Entity\Supplier;
use App\Repository\PlanDeliveryEggRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plans_breeder")
 * @IsGranted("ROLE_USER")
 */
class PlanBreederController extends AbstractController
{
    /**
     * @Route("/{id}", name="plan_breeder")
     */
    public function index(Supplier $supplier, PlanDeliveryEggRepository $deliveryEggRepository): Response
    {
        $plans = $deliveryEggRepository->planBreeder($supplier);

        return $this->render('plan_breeder/index.html.twig', [
            'plans' => $plans,
            'supplier' => $supplier
        ]);
    }

    /**
     * @Route("/week/{id}", name="plan_breeder_week")
     */
    public function week(Supplier $supplier, PlanDeliveryEggRepository $deliveryEggRepository): Response
    {
        $plans = $deliveryEggRepository->planBreederWeek($supplier);

        return $this->render('plan_breeder/week.html.twig', [
            'plans' => $plans,
            'supplier' => $supplier
        ]);
    }
}
