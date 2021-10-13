<?php

namespace App\Controller;

use App\Entity\BreedStandard;
use App\Entity\Herds;
use App\Entity\PlanDeliveryEgg;
use App\Form\PlanDeliveryEggForHerdType;
use App\Form\PlanDeliveryEggType;
use App\Repository\PlanDeliveryEggRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_delivery_egg")
 * @IsGranted("ROLE_USER")
 */
class PlanDeliveryEggController extends AbstractController
{
    /**
     * @Route("/", name="plan_delivery_egg_index", methods={"GET"})
     */
    public function index(PlanDeliveryEggRepository $planDeliveryEggRepository): Response
    {
        return $this->render('plan_delivery_egg/index.html.twig', [
            'plan_delivery_eggs' => $planDeliveryEggRepository->findAll(),
        ]);
    }

    public function getBreedStandard($breed)
    {
        $breedStandardRepository = $this->getDoctrine()->getRepository(BreedStandard::class);
        $breedStandard = $breedStandardRepository->findBy(['breed' => $breed]);
        return $breedStandard;
    }

    public function addPlanForHerd($herd, $weekDay, $eggsNumber)
    {
        $planDeliveryEgg = new PlanDeliveryEgg();
        $planDeliveryEgg->setHerd($herd);
        $planDeliveryEgg->setDeliveryDate($weekDay);
        $planDeliveryEgg->setEggsNumber($eggsNumber);

        return $planDeliveryEgg;
    }

    public function checkPlanForHerd($herd)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $planDeliveryEggRepository = $this->getDoctrine()->getRepository(PlanDeliveryEgg::class);
        $herdPlans = $planDeliveryEggRepository->findBy(['herd' => $herd]);
        if (!empty($herdPlans)) {
            foreach ($herdPlans as $herdPlan) {
                $entityManager->remove($herdPlan);
            }
        }
    }

    /**
     * @Route("/herd/{id}", name="plan_delivery_egg_herd", methods={"GET","POST"})
     */
    public function herd(Herds $herd, Request $request): Response
    {
        $this->checkPlanForHerd($herd);

        $form = $this->createForm(PlanDeliveryEggForHerdType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $breedStandard = $this->getBreedStandard($herd->getBreed());
            $hatchingDate = $herd->getHatchingDate();
            $days = $form['day']->getData();
            foreach ($breedStandard as $breedStandardWeek) {
                $weekNumber = $breedStandardWeek->getWeek() - 1;
                $weekDay = clone $hatchingDate;
                $weekDay->modify('+' . $weekNumber . 'week')->modify('+1 day');

                if (count($days) > 1) {
                    foreach ($days as $day) {
                        $date = clone $weekDay;
                        $date->modify('next ' . $day);
                        $eggsNumber = $herd->getHensNumber() * $breedStandardWeek->getHatchingEggsWeek() / count($days);

                        $planDeliveryEgg = $this->addPlanForHerd($herd, $date, $eggsNumber);
                        $entityManager->persist($planDeliveryEgg);
                    }
                } else {
                    $dayName = $days[0];
                    $weekDay->modify('next ' . $dayName);
                    $eggsNumber = $herd->getHensNumber() * $breedStandardWeek->getHatchingEggsWeek();

                    $planDeliveryEgg = $this->addPlanForHerd($herd, $weekDay, $eggsNumber);

                    $entityManager->persist($planDeliveryEgg);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('herd_delivery_plan_week', ['id' => $herd->getId()]);
        }

        return $this->render('plan_delivery_egg/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="plan_delivery_egg_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planDeliveryEgg = new PlanDeliveryEgg();
        $form = $this->createForm(PlanDeliveryEggType::class, $planDeliveryEgg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planDeliveryEgg);
            $entityManager->flush();

            return $this->redirectToRoute('plan_delivery_egg_index');
        }

        return $this->render('plan_delivery_egg/new.html.twig', [
            'plan_delivery_egg' => $planDeliveryEgg,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_delivery_egg_show", methods={"GET"})
     */
    public function show(PlanDeliveryEgg $planDeliveryEgg): Response
    {
        return $this->render('plan_delivery_egg/show.html.twig', [
            'plan_delivery_egg' => $planDeliveryEgg,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plan_delivery_egg_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlanDeliveryEgg $planDeliveryEgg): Response
    {
        $form = $this->createForm(PlanDeliveryEggType::class, $planDeliveryEgg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plan_delivery_egg_index');
        }

        return $this->render('plan_delivery_egg/edit.html.twig', [
            'plan_delivery_egg' => $planDeliveryEgg,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_delivery_egg_delete", methods={"POST"})
     */
    public function delete(Request $request, PlanDeliveryEgg $planDeliveryEgg): Response
    {
        if ($this->isCsrfTokenValid('delete' . $planDeliveryEgg->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planDeliveryEgg);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_delivery_egg_index');
    }
}
