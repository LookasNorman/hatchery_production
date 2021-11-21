<?php

namespace App\Controller;

use App\Entity\PlanDeliveryChick;
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
            $plans = $this->chickInWeek($date);
            array_push($weeks, ['week' => $i, 'plans' => $plans]);
            $date->modify('next Monday');
        }

        return $this->render('plan/index.html.twig', [
            'weeks' => $weeks
        ]);
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
