<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanIndicators;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanInputController extends AbstractController
{
    public function getIndicators()
    {
        $planIndicatorsRepository = $this->getDoctrine()->getRepository(PlanIndicators::class);
        return $planIndicatorsRepository->findOneBy([]);
    }

    public function getPlans($date)
    {
        $date->modify('midnight');
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $breed = $this->getDoctrine()->getRepository(Breed::class)->findOneBy([]);
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $planDeliveryChicks = $planDeliveryChickRepository->planInputsInDay($date, $end, $breed);
        return $planDeliveryChicks;
    }

    /**
     * @Route("/plan_input", name="plan_input")
     */
    public function index(): Response
    {
        $date = new \DateTime();
        $indicators = $this->getIndicators();
        $deliveriesChicks = $this->getPlans($date);

        return $this->render('plans/inputs/index.html.twig', [
            'indicators' => $indicators,
            'deliveriesChicks' => $deliveriesChicks
        ]);
    }
}
