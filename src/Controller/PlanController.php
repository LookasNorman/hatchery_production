<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputsFarmDelivery;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanDeliveryEgg;
use App\Entity\PlanIndicators;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("plans")
 * @IsGranted("ROLE_USER")
 */
class PlanController extends AbstractController
{
    public function inputsInDay($date, $breed)
    {
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $plans = $planDeliveryChickRepository->planInputsInDay($date, $end, $breed);

        return $plans;
    }

    public function inputsInWeek($date, $breed)
    {
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $end = clone $date;
        $end->modify('next Monday -1 second');
        $plans = $planDeliveryChickRepository->planInputsInDay($date, $end, $breed);

        return $plans;
    }

    public function chicksInPlans($plans)
    {
        $chicks = 0;
        foreach ($plans as $plan) {
            $chicks = $chicks + $plan->getChickNumber();
        }
        return $chicks;
    }

    public function deliveryInDay($date)
    {
        $planeDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $deliveries = $planeDeliveryEggRepository->planDeliveryInDay($date, $end);

        return $deliveries;
    }

    public function deliveryInWeek($date, $breed)
    {
        $planeDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $end = clone $date;
        $end->modify('next Monday -1 second');
        $deliveries = $planeDeliveryEggRepository->planDeliveryInDay($date, $end, $breed);

        return $deliveries;
    }

    public function herdDeliveryInDay($date, $herd)
    {
        $planeDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $deliveries = $planeDeliveryEggRepository->herdPlanDeliveryInDay($date, $end, $herd);

        return $deliveries;
    }

    public function eggsInDeliveries($deliveries)
    {
        $eggs = 0;
        foreach ($deliveries as $delivery) {
            $eggs = $eggs + $delivery->getEggsNumber();
        }
        return $eggs;
    }

    public function eggsInWarehouse($eggsOnWarehouse, $chicks, $breed)
    {
        $planIndicatorsRepository = $this->getDoctrine()->getRepository(PlanIndicators::class);
        $planIndicators = $planIndicatorsRepository->findOneBy([]);
        $hatchability = $planIndicators->getHatchability();
        $eggsToProduction = $chicks / ($hatchability / 100);
        if ($eggsOnWarehouse == 0) {
            $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
            $deliveredEggs = (int)$deliveryRepository->eggsBreedDelivered($breed);
            $inputFarmDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
            $productionsEggs = (int)$inputFarmDeliveryRepository->eggsBreedProduction($breed);
            $eggsOnWarehouse = $deliveredEggs - $productionsEggs;
        }

        return $eggsOnWarehouse - $eggsToProduction;
    }

    /**
     * @Route("/herd_delivery/{id}", name="herd_delivery_plan_week", methods={"GET"})
     */
    public function herdDeliveryIndex(Herds $herd)
    {
        $now = new \DateTime('today midnight');
        $nowWeek = (int)$now->format('W');

        $weeksPlans = [];
        for ($i = $nowWeek; $i <= 52; $i++) {
            $monday = new \DateTime('midnight');
            $monday->setISODate(2021, $i);
            $date = clone $monday;
            $daysPlans = [];
            for ($j = 0; $j < 7; $j++) {
                $dayDeliveries = $this->herdDeliveryInDay($date, $herd);
                $eggs = $this->eggsInDeliveries($dayDeliveries);
                if ($date >= $now) {
                    array_push($daysPlans, [
                        'date' => $date,
                        'dayDeliveries' => $dayDeliveries,
                        'eggs' => $eggs,
                    ]);
                }
                $date = clone $date;
                $date->modify('+1 days');
            }
            array_push($weeksPlans, ['week' => $i, 'weekPlans' => $daysPlans]);
        }
        return $this->render('plans/herd_index.html.twig', [
            'weeksPlans' => $weeksPlans,
            'herd' => $herd
        ]);
    }

    public function yearIndexByWeek($now, $breed)
    {
        if ($now->format('d') == 1 and $now->format('M') == 'Jan') {
            $now->modify('Monday next week');
        }
        $nowWeek = (int)$now->format('W');
        $year = (int)$now->format('Y');
        $eggsOnWarehouse = 0;

        $weeksPlans = [];
        for ($i = $nowWeek; $i <= 52; $i++) {
            if ($i === $nowWeek) {
                $date = clone $now;
            } else {
                $monday = new \DateTime('midnight');
                $monday->setISODate($year, $i);
                $date = clone $monday;
            }
            $dayPlans = $this->inputsInWeek($date, $breed);
            $chicks = $this->chicksInPlans($dayPlans);
            $dayDeliveries = $this->deliveryInWeek($date, $breed);
            $eggs = $this->eggsInDeliveries($dayDeliveries);
            if ($date >= $now) {
                $eggsOnWarehouse = $this->eggsInWarehouse($eggsOnWarehouse, $chicks, $breed);
                $eggsOnWarehouse = $eggsOnWarehouse + $eggs;
                $weekPlans = [
                    'date' => $date,
                    'dayPlans' => $dayPlans,
                    'chicks' => $chicks,
                    'dayDeliveries' => $dayDeliveries,
                    'eggs' => $eggs,
                    'eggsOnWarehouse' => $eggsOnWarehouse
                ];
            }
            $date = clone $date;
            $date->modify('+1 days');
            array_push($weeksPlans, ['week' => $i, 'weekPlans' => $weekPlans]);
        }

        return $weeksPlans;
    }

    public function yearIndex($now)
    {
        if ($now->format('d') == 1 and $now->format('M') == 'Jan') {
            $now->modify('Monday next week');
        }
        $nowWeek = (int)$now->format('W');
        $year = (int)$now->format('Y');
        $eggsOnWarehouse = 0;

        $weeksPlans = [];
        for ($i = $nowWeek; $i <= 52; $i++) {
            $monday = new \DateTime('midnight');
            $monday->setISODate($year, $i);
            $date = clone $monday;
            $daysPlans = [];
            for ($j = 0; $j < 7; $j++) {
                $dayPlans = $this->inputsInDay($date);
                $chicks = $this->chicksInPlans($dayPlans);
                $dayDeliveries = $this->deliveryInDay($date);
                $eggs = $this->eggsInDeliveries($dayDeliveries);
                if ($date >= $now) {
                    $eggsOnWarehouse = $this->eggsInWarehouse($eggsOnWarehouse, $chicks);
                    array_push($daysPlans, [
                        'date' => $date,
                        'dayPlans' => $dayPlans,
                        'chicks' => $chicks,
                        'dayDeliveries' => $dayDeliveries,
                        'eggs' => $eggs,
                        'eggsOnWarehouse' => $eggsOnWarehouse
                    ]);
                    $eggsOnWarehouse = $eggsOnWarehouse + $eggs;
                }
                $date = clone $date;
                $date->modify('+1 days');
            }
            array_push($weeksPlans, ['week' => $i, 'weekPlans' => $daysPlans]);
        }

        return $weeksPlans;
    }

    public function getBreed()
    {
        $breedRepository = $this->getDoctrine()->getRepository(Breed::class);
        $breeds = $breedRepository->findAll();

        return $breeds;
    }

    /**
     * @Route("/", name="plan_week", methods={"GET"})
     */
    public function index()
    {
        $indicatorsRepository = $this->getDoctrine()->getRepository(PlanIndicators::class);
        $indicators = $indicatorsRepository->findOneBy([]);

        $breeds = $this->getBreed();

        $now = new \DateTime('today midnight');
        $thisYear = (int)$now->format('Y');

        $next = clone $now;
        $next->modify('first day of january next year');
        $nextYear = (int)$next->format('Y');

        $second = clone $next;
        $second->modify('first day of january next year');
        $secondYear = $second->format('Y');

        $breedsPlans = [];
        foreach ($breeds as $breed){
            $weeksPlans = $this->yearIndexByWeek($now, $breed);
            $weeksPlansNextYear = $this->yearIndexByWeek($next, $breed);
            $weeksPlansSecondYear = $this->yearIndexByWeek($second, $breed);

            $yearsPlans = [
                $thisYear => $weeksPlans,
                $nextYear => $weeksPlansNextYear,
                $secondYear => $weeksPlansSecondYear
            ];
            $breedPlans = ['breed' => $breed, 'yearsPlans' => $yearsPlans];

            array_push($breedsPlans, $breedPlans);
        }

        return $this->render('plans/index.html.twig', [
            'indicators' => $indicators,
            'breedsPlans' => $breedsPlans
        ]);
    }
}
