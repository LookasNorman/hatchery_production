<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputsFarmDelivery;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanDeliveryEgg;
use App\Entity\PlanIndicators;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    public function deliveryInDay($date, $breed)
    {
        $planeDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $end = clone $date;
        $end->modify('+1 day -1 second');
        $deliveries = $planeDeliveryEggRepository->planDeliveryInDay($date, $end, $breed);

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

    public function herdDeliveryInWeek($date, $herd)
    {
        $planeDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $end = clone $date;
        $end->modify('next Monday -1 second');
        $deliveries = $planeDeliveryEggRepository->herdPlanDeliveryInDay($date, $end, $herd);

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

        return $this->render('plans/herd_index.html.twig', [
            'weeksPlans' => $weeksPlans,
            'herd' => $herd
        ]);
    }

    /**
     * @Route("/herd_delivery/{id}/pdf", name="herd_delivery_plan_week_pdf", methods={"GET"})
     */
    public function herdDeliveryPdf(Herds $herd, Pdf $pdf, TranslatorInterface $translator)
    {
        $planDeliveryRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $plans = $planDeliveryRepository->findBy(['herd' => $herd], ['deliveryDate' => 'ASC']);
        $html = $this->renderView('plans/pdf/herd_deliver.html.twig', [
            'plans' => $plans,
            'herd' => $herd
        ]);
        $title = $translator->trans('plans.pdf.herd_delivery.title');

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            $title
        );
    }

    public function details($now, $breed)
    {
        $end = clone $now;
        $end->modify('+ 2 week');
        $end->modify('Monday next week');

        $eggsOnWarehouse = 0;

        $weeksPlans = [];
        for ($i = clone $now; $i <= $end; $i->modify('Monday next week')) {
            $dateStart = clone $i;
            $endDate = clone $dateStart;
            $endDate->modify('next Monday -1 second');
            $daysPlans = [];
            for ($j = clone $dateStart; $j < $endDate; $j->modify('+1 day')) {
                $dayPlans = $this->inputsInDay($j, $breed);
                $chicks = $this->chicksInPlans($dayPlans);
                $dayDeliveries = $this->deliveryInDay($j, $breed);
                $eggs = $this->eggsInDeliveries($dayDeliveries);
                if ($j >= $now) {
                    $date = clone $j;
                    $eggsOnWarehouse = $this->eggsInWarehouse($eggsOnWarehouse, $chicks, $breed);
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
            }
            $weekNumber = (int)$i->format('W');
            array_push($weeksPlans, ['week' => $weekNumber, 'weekPlans' => $daysPlans]);
        }

        return $weeksPlans;
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

    public function getBreed()
    {
        $breedRepository = $this->getDoctrine()->getRepository(Breed::class);
        $breeds = $breedRepository->findAll();

        return $breeds;
    }

    public function weekChicksPlanBreed($year, $breed)
    {
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $now = new \DateTime('midnight');
        if ($year < $now) {
            $firstWeekStart = $now;
            if ($firstWeekStart->format('l') != 'Monday') {
                $firstWeekStart->modify('previous Monday');
            }
        } else {
            $firstWeekStart = clone $year;
            if ($firstWeekStart->format('l') != 'Monday') {
                $firstWeekStart->modify('next Monday');
            }
        }

        $firstWeekEnd = clone($firstWeekStart);
        $firstWeekEnd->modify('first day of january next year');

        if ($firstWeekEnd->format('l') != 'Monday') {
            $firstWeekEnd->modify('next Monday');
        }

        $plans = $planDeliveryChickRepository->planInputsInWeekCustomer($breed, $firstWeekStart, $firstWeekEnd);

        return $plans;
    }

    public function weekEggsPlanBreed($year, $breed)
    {
        $planDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $now = new \DateTime();
        $now->modify('midnight -1 second');
        if ($year < $now) {
            $firstWeekStart = $now;
            if ($firstWeekStart->format('l') != 'Monday') {
                $firstWeekStart->modify('previous Monday');
            }
        } else {
            $firstWeekStart = clone $year;
            if ($firstWeekStart->format('l') != 'Monday') {
                $firstWeekStart->modify('next Monday');
            }
        }

        $firstWeekEnd = clone($firstWeekStart);
        $firstWeekEnd->modify('first day of january next year');

        if ($firstWeekEnd->format('l') != 'Monday') {
            $firstWeekEnd->modify('next Monday');
        }


        $plans = $planDeliveryEggRepository->planInputsInWeekHerd($breed, $firstWeekStart, $firstWeekEnd);

        return $plans;
    }

    public function sortPlansByWeek($year, $breed)
    {
        $now = new \DateTime('midnight');
        if ($year < $now) {
            $firstWeekStart = $now;
            if ($firstWeekStart->format('l') != 'Monday') {
                $firstWeekStart->modify('previous Monday');
            }
        } else {
            $firstWeekStart = clone $year;
            if ($firstWeekStart->format('l') != 'Monday') {
                $firstWeekStart->modify('next Monday');
            }
        }

        $chickPlans = $this->weekChicksPlanBreed($year, $breed);
        $eggsPlans = $this->weekEggsPlanBreed($year, $breed);

        $firstWeekNumber = (int)$firstWeekStart->format('W');
        $plansArray = [];
        for ($i = $firstWeekNumber; $i <= 52; $i++) {
            $weekChicksArray = [];
            $weekEggsArray = [];
            $weekChicks = 0;
            foreach ($chickPlans as $chickPlan) {
                if ($chickPlan['yearWeek'] == $i) {
                    $weekChicks = $weekChicks + $chickPlan['chicks'];
                    array_push($weekChicksArray, $chickPlan);
                }
            }
            $weekEggs = 0;
            foreach ($eggsPlans as $eggPlan) {
                if ($eggPlan['yearWeek'] == $i) {
                    $weekEggs = $weekEggs + $eggPlan['eggs'];
                    array_push($weekEggsArray, $eggPlan);
                }
            }
            $date = new \DateTime();
            $date->setISODate($year->format('Y'), $i);
            $date->modify('midnight');
            $eggsInStock = $this->eggsInStock($breed, $date);

            $plansArray[$i] = [
                'eggsInStock' => $eggsInStock,
                'date' => $date,
                'chickPlans' => $weekChicksArray,
                'eggPlans' => $weekEggsArray,
                'chicks' => $weekChicks,
                'eggs' => $weekEggs
            ];

        }

        return $plansArray;
    }

    public function planEggs(Breed $breed, $dateEnd)
    {
        $indicators = $this->getDoctrine()->getRepository(PlanIndicators::class)->findOneBy([]);

        $dateStart = new \DateTime();
        $eggsRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $chicksRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $eggs = $eggsRepository->planBreedBeetwenDate($breed, $dateStart, $dateEnd);

        $chicks = $chicksRepository->planBreedBetweenDate($breed, $dateStart, $dateEnd);
        $planEgg = $eggs - ($chicks / ($indicators->getHatchability() / 100));

        return $planEgg;
    }

    public function eggsInStock(Breed $breed, $date)
    {
        $dateEnd = clone $date;
        $dateEnd->modify('-1 second');
        $planEggs = $this->planEggs($breed, $dateEnd);

        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        $inputDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
        $eggsDelivered = $deliveryRepository->eggsBreedDelivered($breed);
        $eggsInProduction = $inputDeliveryRepository->eggsBreedProduction($breed);
        $eggsStock = $eggsDelivered - $eggsInProduction + $planEggs;

        return $eggsStock;

    }

    public function yearLink()
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

    /**
     * @Route("/year_plan/{yearN}", name="plan_year_plan", methods={"GET"})
     */
    public function showYearPlans($yearN = null)
    {
        $links = $this->yearLink();

        if (isset($yearN)) {
            $year = new \DateTime();
            $year->modify('1st January ' . $yearN);
        } else {
            $year = new \DateTime();
        }
        $breeds = $this->getBreed();

        foreach ($breeds as $breed) {
            $plans[$breed->getName() . $breed->getType()] = $this->sortPlansByWeek($year, $breed);
        }

        return $this->render('plans/yearPlanSite.html.twig', [
            'year' => $year,
            'plans' => $plans,
            'links' => $links,
        ]);
    }

    /**
     * @Route("/week/{year}/{week}", name="plan_week_detail", methods={"GET"})
     */
    public function showWeek($week, $year)
    {
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $planDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $breed = $this->getDoctrine()->getRepository(Breed::class)->find(1);
        $date = new \DateTime();
        $date->setISODate($year, $week, 1);
        $date->modify('midnight');
        $dateEnd = clone $date;
        $dateEnd->modify('+7 days');

        $plans = [];
        for ($day = clone $date; $day < $dateEnd; $day->modify('+1 day')) {
            $start = clone $day;
            $start->modify('midnight');
            $end = clone $day;
            $end->modify('midnight +1 day -1 second');
//dump($start);
//dump($end);
            array_push($plans, [
                'date' => $start,
                'chicks' => $planDeliveryChickRepository->planInputsInDay($start, $end, $breed),
                'eggs' => $planDeliveryEggRepository->planDeliveryInDay($start, $end, $breed)
            ]);
        }
//die();
        return $this->render('plans/show_week/index.html.twig', [
            'date' => $date,
            'plans' => $plans,
            'breed' => $breed
        ]);
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
        foreach ($breeds as $breed) {
            $detailsPlans = $this->details($now, $breed);

            $yearsPlans = [
                'details' => $detailsPlans,
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
