<?php

namespace App\Controller;

use App\Entity\PlanDeliveryChick;
use App\Entity\PlanIndicators;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("plans")
 */
class PlanController extends AbstractController
{
    public function inputsInDay($date)
    {
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $end = clone $date;
        $end->modify('+1 day');
        $plans = $planDeliveryChickRepository->planInputsInDay($date, $end);

        return $plans;
    }

    /**
     * @Route("/", name="plan_week", methods={"GET"})
     */
    public function index()
    {
        $indicatorsRepository = $this->getDoctrine()->getRepository(PlanIndicators::class);
        $indicators = $indicatorsRepository->findOneBy([]);
        $now = new \DateTime('today midnight');
        $nowWeek = (int)$now->format('W');

        $weeksPlans = [];
        for ($i = $nowWeek; $i <= 52; $i++) {
            $monday = new \DateTime('midnight');
            $monday->setISODate(2021, $i);
            $date = clone $monday;
            $daysPlans = [];
            for ($j = 0; $j < 7; $j++) {
                $dayPlans = $this->inputsInDay($date);
                array_push($daysPlans, ['date' => $date, 'dayPlans' => $dayPlans]);
                $date = clone $date;
                $date->modify('+1 days');
            }
            array_push($weeksPlans, ['week' => $i, 'weekPlans' => $daysPlans]);
        }
//        dd($indicators);
        return $this->render('plans/index.html.twig', [
            'weeksPlans' => $weeksPlans,
            'indicators' => $indicators
        ]);
    }
}
