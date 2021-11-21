<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanDeliveryEgg;
use App\Entity\SellingEgg;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan")
 * @IsGranted("ROLE_USER")
 */
class PlanController extends AbstractController
{
    /**
     * @Route("/", name="plan")
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
     * @Route("/week/{week}", name="plan_week")
     */
    public function week($week)
    {
        $date = new \DateTime('midnight');
        $date->setISODate('2021', $week);
        $eggStock = $this->getEggStock();
        $plans = [];
        for($i=1; $i <=7; $i++){
            $day = clone $date;
            $eggDelivery = $this->getEggDeliveryInDay($day);
            $chickInputs = $this->getChickDeliveryInDay($day);
            array_push($plans, ['day' => $day, 'stocks' => $eggStock, 'deliveries' => $eggDelivery, 'chicks' => $chickInputs]);
            $date->modify('+1 day');
            $eggStock = [];
        }
        return $this->render('plan/week.html.twig', [
            'plans' => $plans
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

    public function getChickDeliveryInDay($date)
    {
        $start = clone $date;
        $start->modify('-1 second');
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $chickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        return $plans = $chickRepository->planInputBetweenDateForPlan($start, $end);
    }

    public function getEggDeliveryInDay($date)
    {
        $start = clone $date;
        $start->modify('-1 second');
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $deliveryRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        return $plans = $deliveryRepository->planBetweenDateForPlan($start, $end);
    }

    public function eggsOnWarehouse($deliveries)
    {
        $inputDeliveryRepository = $this->getDoctrine()->getRepository(InputDelivery::class);
        $sellingEggDelivery = $this->getDoctrine()->getRepository(SellingEgg::class);

        $eggsDeliveries = [];
        foreach ($deliveries as $delivery) {
            $eggsInProduction = $inputDeliveryRepository->eggsFromDelivery($delivery);
            $eggsSelled = $sellingEggDelivery->eggsFromDelivery($delivery);

            $inputsDeliveries = $delivery->getEggsNumber() - $eggsInProduction - $eggsSelled;
            if ($inputsDeliveries > 0) {
                array_push($eggsDeliveries, ['delivery' => $delivery, 'eggs' => (int)$inputsDeliveries]);
            } elseif (is_null($inputsDeliveries)) {
                array_push($eggsDeliveries, ['delivery' => $delivery, 'eggs' => (int)$delivery->getEggsNumber()]);
            }
        }

        return $eggsDeliveries;
    }

    public function getEggStock()
    {
        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        $deliveries = $deliveryRepository->findBy([], ['deliveryDate' => 'desc']);
        $eggsDeliveries = $this->eggsOnWarehouse($deliveries);

        return $eggsDeliveries;
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
