<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\Herds;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanDeliveryEgg;
use App\Repository\DeliveryRepository;
use App\Repository\PlanDeliveryEggRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanDetailController extends AbstractController
{

    public function getHerdInPlan($breed, $date)
    {
        $herdRepositury = $this->getDoctrine()->getRepository(Herds::class);
        $planHerds = $herdRepositury->getHerdInPlanBreed($breed, $date);
        return $planHerds;
    }

    public function planEggsBreedWeek($breed)
    {
        $now = new \DateTime('midnight');
        $planDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $planHerds = $this->getHerdInPlan($breed, $now);
        $planEggs = [];
        foreach ($planHerds as $planHerd) {
            $planHerdEggs = $planDeliveryEggRepository->planHerdWeekAfterDate($planHerd, $now);
            foreach ($planHerdEggs as $planHerdEgg){
                array_push($planEggs, ['weekYear' => $planHerdEgg['weekYear'], $planHerdEgg]);
            }
        }

        return $planEggs;
    }

    public function sumWeekEggs($planEggs)
    {
        $eggs= [];
        foreach ($planEggs as $planEgg){
            $eggsSum = array_sum(array_column($planEgg['eggs'], 'eggsNumber'));
            array_push($eggs, ["weekYear" => $planEgg['weekYear'], 0 => ['eggsStock' => $eggsSum]]);
        }
        return $eggs;
    }

    public function planChickBreedWeek($breed)
    {
        $now = new \DateTime('midnight');
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $planChicks = $planDeliveryChickRepository->planBreedFarm($breed, $now);
        return $planChicks;
    }

    /**
     * @Route("/plan_detail", name="plan_detail")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $breed = $this->getDoctrine()->getRepository(Breed::class)->findBy(['name' => 'Ross']);
        $planEggs = $this->planEggsBreedWeek($breed);
        $planChicks = $this->planChickBreedWeek($breed);
        $plan = [];

        foreach ($planEggs as $planEgg) {
            $id = $planEgg['weekYear'];
            if (!isset($plan[$id])) {
                $plan[$id] = $planEgg;
                $plan[$id][0] = array();
            }
            $plan[$id]['eggs'][] = $planEgg[0];
        }
        $weekEggs = $this->sumWeekEggs($plan);

        foreach ($weekEggs as $weekEgg) {
            $id = $weekEgg['weekYear'];
            if (!isset($plan[$id])) {
                $plan[$id] = $weekEgg;
                $plan[$id][0] = array();
            }
            $plan[$id]['eggsStock'][] = $weekEgg[0];
        }

        foreach ($planChicks as $planChick) {
            $id = $planChick['weekYear'];
            if (!isset($plan[$id])) {
                $plan[$id] = $planChick;
                $plan[$id][0] = array();
            }
            $plan[$id]['chicks'][] = $planChick[0];
        }

        return $this->render('plan_detail/index.html.twig', [
            'plans' => $plan,
        ]);
    }
}
