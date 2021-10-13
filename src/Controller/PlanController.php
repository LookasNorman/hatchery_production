<?php

namespace App\Controller;

use App\Entity\Delivery;
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
    public function inputsInDay($date)
    {
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $end = clone $date;
        $end->modify('+1 day');
        $plans = $planDeliveryChickRepository->planInputsInDay($date, $end);

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

    public function eggsInDeliveries($deliveries)
    {
        $eggs = 0;
        foreach ($deliveries as $delivery) {
            $eggs = $eggs + $delivery->getEggsNumber();
        }
        return $eggs;
    }

    public function eggsInWarehouse($eggsOnWarehouse, $chicks, $eggs)
    {
        $planIndicatorsRepository = $this->getDoctrine()->getRepository(PlanIndicators::class);
        $planIndicators = $planIndicatorsRepository->findOneBy([]);
        $hatchability = $planIndicators->getHatchability();
        $eggsToProduction = $chicks / ($hatchability / 100);
        if($eggsOnWarehouse == 0){
            $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
            $eggsOnWarehouse = $deliveryRepository->eggsInWarehouse()[0]['eggsInWarehouse'];
        }

        return $eggsOnWarehouse + $eggs - $eggsToProduction;
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
        $eggsOnWarehouse = 0;

        $weeksPlans = [];
        for ($i = $nowWeek; $i <= 52; $i++) {
            $monday = new \DateTime('midnight');
            $monday->setISODate(2021, $i);
            $date = clone $monday;
            $daysPlans = [];
            for ($j = 0; $j < 7; $j++) {

                $dayPlans = $this->inputsInDay($date);
                $chicks = $this->chicksInPlans($dayPlans);
                $dayDeliveries = $this->deliveryInDay($date);
                $eggs = $this->eggsInDeliveries($dayDeliveries);
                if($date >= $now){
                    $eggsOnWarehouse = $this->eggsInWarehouse($eggsOnWarehouse, $chicks, $eggs);
                    array_push($daysPlans, [
                    'date' => $date,
                    'dayPlans' => $dayPlans,
                    'chicks' => $chicks,
                    'dayDeliveries' => $dayDeliveries,
                    'eggs' => $eggs,
                    'eggsOnWarehouse' => $eggsOnWarehouse
                ]);}
                $date = clone $date;
                $date->modify('+1 days');
            }
            array_push($weeksPlans, ['week' => $i, 'weekPlans' => $daysPlans]);
        }

        return $this->render('plans/index.html.twig', [
            'weeksPlans' => $weeksPlans,
            'indicators' => $indicators
        ]);
    }
}
