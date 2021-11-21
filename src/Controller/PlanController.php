<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanDeliveryEgg;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanController extends AbstractController
{
    /**
     * @Route("/plan", name="plan")
     */
    public function index(): Response
    {
        $date = new \DateTime('midnight');
        if ($date->format('l') != 'Monday') {
            $date->modify('previous Monday');
        }
        $weeks = [];
        for ($i = (int)$date->format('W'); $i <= 52; $i++) {
            $chickPlans = $this->chickInWeek($date);
            $eggPlans = $this->eggsInWeek($date);
            array_push($weeks, ['week' => $i, 'plans' => ['chick' => $chickPlans, 'egg' => $eggPlans]]);
            $date->modify('next Monday');
        }

        return $this->render('plan/index.html.twig', [
            'weeks' => $weeks
        ]);
    }

    /**
     * @Route("/herd_delivery/{id}", name="herd_delivery_plan_week", methods={"GET"})
     */
    public function herdDeliveryIndex(Herds $herd)
    {
        $hatchingDate = $herd->getHatchingDate();

        $startDelivery = clone $hatchingDate;
        $startDelivery->modify('+25 week');
        $startDelivery->modify('previous monday');
        $date = clone $startDelivery;
        $weeksPlans = [];
        for ($i = 1; $i <= 40; $i++) {

            $dayDeliveries = $this->herdDeliveryInWeek($date, $herd);
            $eggs = $this->eggsInDeliveries($dayDeliveries);
            $weekPlans = [
                'date' => $date,
                'dayDeliveries' => $dayDeliveries,
                'eggs' => $eggs,
            ];
            $date = clone $date;
            $date->modify('next Monday');
            array_push($weeksPlans, ['week' => $i, 'weekPlans' => $weekPlans]);
        }

        return $this->render('plan_delivery_egg/herd_index.html.twig', [
            'weeksPlans' => $weeksPlans,
            'herd' => $herd
        ]);
    }

    public function eggsInDeliveries($deliveries)
    {
        $eggs = 0;
        foreach ($deliveries as $delivery) {
            $eggs = $eggs + $delivery->getEggsNumber();
        }
        return $eggs;
    }

    public function herdDeliveryInWeek($date, $herd)
    {
        $planeDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $end = clone $date;
        $end->modify('next Monday -1 second');
        $deliveries = $planeDeliveryEggRepository->herdPlanDeliveryInDay($date, $end, $herd);

        return $deliveries;
    }

    public function eggsInWeek($mondayDate)
    {
        $eggsPlanRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $sundayDate = clone $mondayDate;
        $sundayDate->modify('next Monday -1 second');
        $eggPlans = $eggsPlanRepository->planBetweenDateForPlan($mondayDate, $sundayDate);
        return $eggPlans;
    }

    public function chickInWeek($mondayDate)
    {
        $chickPlanRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $sundayDate = clone $mondayDate;
        $sundayDate->modify('next Monday -1 second');
        $chickPlans = $chickPlanRepository->planBetweenDateForPlan($mondayDate, $sundayDate);
        return $chickPlans;
    }
}
