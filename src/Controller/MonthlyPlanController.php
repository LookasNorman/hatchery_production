<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\InputDelivery;
use App\Entity\InputsFarm;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanDeliveryEgg;
use App\Entity\SellingEgg;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/monthly_plan")
 */
class MonthlyPlanController extends AbstractController
{
    /**
     * @Route("/{yearN}", name="monthly_plan")
     */
    public function index($yearN = null): Response
    {
        $links = $this->yearLink();
        $now = new \DateTime('midnight');
        $startMonth = clone $now;
        if (!isset($yearN) or $yearN === $now->format('Y')) {
            $startMonth->modify('first day of previous month');
        } else {
            $startMonth->modify('1st January ' . $yearN);
        }
        $monthlyData = $this->monthlyData($startMonth);
        $monthlySortedData = $this->monthlySortedData($startMonth, $monthlyData);

        return $this->render('monthly_plan/index.html.twig', [
            'links' => $links,
            'start_date' => $startMonth,
            'monthlyData' => $monthlySortedData,
        ]);
    }

    public function previousPlanEggAndChick($date)
    {
        $date->modify('-1 second');
        $now = new \DateTime('midnight');
        $eggs = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class)->eggBetweenDate($now, $date);
        $chicks = $this->getDoctrine()->getRepository(PlanDeliveryChick::class)->chickBetweenDate($now, $date);
        $eggsOnStock = $this->eggsOnStock();
        return $eggsOnStock + $eggs - $chicks / 0.8;
    }

    public function monthlySortedData($startDate, $data)
    {
        $eggsOnStock = $this->eggsOnStock();
        $now = new \DateTime('midnight');
        if($startDate->format('M') < $now->format('M') AND $startDate->format('Y') == $now->format('Y')){
            $start = clone $now;
        } else {
            $start = clone $startDate;
        }
        $sortedData = [];
        for($i=(int)$start->format('m'); $i<=12; $i++){
            $planInputChickData = [];
            $planDeliveryChickData = [];
            $planEggData = [];
            $deliveredChickData = [];
            $deliveredEggData = [];
            $eggsOnStockData = [];
            $month = $start->format('Y') . sprintf("%02d", $i);
            foreach ($data['planInputChicks'] as $planInputChick){
                if($planInputChick['month'] == $month){
                    $planInputChickData = $planInputChick;
                }
            }
            foreach ($data['planDeliveryChicks'] as $planDeliveryChick){
                if($planDeliveryChick['month'] == $month){
                    $planDeliveryChickData = $planDeliveryChick;
                }
            }
            foreach ($data['planEgg'] as $planEgg){
                if($planEgg['month'] == $month){
                    $planEggData = $planEgg;
                }
            }
            foreach ($data['deliveredChick'] as $deliveredChick){
                if($deliveredChick['month'] == $month){
                    $deliveredChickData = $deliveredChick;
                }
            }
            foreach ($data['deliveredEgg'] as $deliveredEgg){
                if($deliveredEgg['month'] == $month){
                    $deliveredEggData = $deliveredEgg;
                }
            }
            if($start->format('Y') == $now->format('Y')) {
                if ((int)$now->format('m') <= $i) {
                    $eggsOnStockData = ['month' => $month, 'eggsNumber' => $eggsOnStock];
                    $eggsOnStock = $eggsOnStock + $planEggData['eggs'] - $planInputChickData['chickNumber'] / 0.8;
                }
            } else {
                if($i == 1) {
                    $eggsOnStock = $this->previousPlanEggAndChick(clone $start);
                    $eggsOnStockData = ['month' => $month, 'eggsNumber' => $eggsOnStock];
                    $eggsOnStock = $eggsOnStock + $planEggData['eggs'] - $planInputChickData['chickNumber'] / 0.8;
                }
                else {
                    $eggsOnStockData = ['month' => $month, 'eggsNumber' => $eggsOnStock];
                    $eggsOnStock = $eggsOnStock + $planEggData['eggs'] - $planInputChickData['chickNumber'] / 0.8;
                }
            }
            $sortedData[$month] = [
                'planInputChick' => $planInputChickData,
                'planDeliveryChick' => $planDeliveryChickData,
                'planEgg' => $planEggData,
                'deliveredChick' => $deliveredChickData,
                'deliveredEgg' => $deliveredEggData,
                'eggsOnStock' => $eggsOnStockData,
            ];
        }
        return $sortedData;
    }

    public function monthlyData($startDate): array
    {
        $now = new \DateTime('midnight');
        if($startDate < $now){
            $start = clone $now;
        } else {
            $start = clone $startDate;
        }
        $end = clone $start;
        $end->modify('1st January next year -1 second');

        $planInputChicks = $this->monthlyPlanInputChicks($start, $end);
        $planDeliveryChicks = $this->monthlyPlanDeliveryChicks($start, $end);
        $planEgg = $this->monthlyPlanEgg($start, $end);
        $deliveredChick = $this->monthlyDeliveredChick(clone $startDate, clone $now);
        $deliveredEgg = $this->monthlyDeliveredEgg(clone $startDate, clone $now);

        return [
            'planInputChicks' => $planInputChicks,
            'planDeliveryChicks' => $planDeliveryChicks,
            'planEgg' => $planEgg,
            'deliveredChick' => $deliveredChick,
            'deliveredEgg' => $deliveredEgg,
        ];
    }

    public function eggsOnStock()
    {
        $deliveredEggs = $this->getDoctrine()->getRepository(Delivery::class)->eggsDelivered();
        $sellingEggs = $this->getDoctrine()->getRepository(SellingEgg::class)->sellingEggs();
        $inputEggs = $this->getDoctrine()->getRepository(InputDelivery::class)->eggsProduction();
        return $eggsOnStock = $deliveredEggs - $sellingEggs - $inputEggs;
    }

    public function monthlyDeliveredEgg($start, $end)
    {
        return $this->getDoctrine()->getRepository(Delivery::class)->monthlyDeliveredEgg($start, $end);
    }

    public function monthlyDeliveredChick($start, $end)
    {
        return $this->getDoctrine()->getRepository(InputsFarm::class)->monthlyDeliveredChick($start, $end);
    }

    public function monthlyPlanEgg($start, $end)
    {
        return $this->getDoctrine()->getRepository(PlanDeliveryEgg::class)->monthlyPlanEgg($start, $end);
    }

    public function monthlyPlanDeliveryChicks($start, $end)
    {
        return $this->getDoctrine()->getRepository(PlanDeliveryChick::class)->planMonthlyDeliveryFarm($start, $end);
    }

    public function monthlyPlanInputChicks($start, $end)
    {
        return $this->getDoctrine()->getRepository(PlanDeliveryChick::class)->planMonthlyInputFarm($start, $end);
    }

    public function yearLink(): array
    {
        $first = new \DateTime();
        $second = new \DateTime('1st January next Year');
        $third = new \DateTime('1st January +2 year');

        return [
            $first->format('Y'),
            $second->format('Y'),
            $third->format('Y'),
        ];
    }
}