<?php

namespace App\Controller;

use App\Entity\PlanDeliveryEgg;
use App\Form\PlanDeliveryEggType;
use App\Repository\PlanDeliveryEggRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_delivery_egg")
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
        if ($this->isCsrfTokenValid('delete'.$planDeliveryEgg->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planDeliveryEgg);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_delivery_egg_index');
    }
}
