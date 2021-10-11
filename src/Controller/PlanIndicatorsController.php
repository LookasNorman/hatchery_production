<?php

namespace App\Controller;

use App\Entity\PlanIndicators;
use App\Form\PlanIndicatorsType;
use App\Repository\PlanIndicatorsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_indicators")
 */
class PlanIndicatorsController extends AbstractController
{
    /**
     * @Route("/", name="plan_indicators_index", methods={"GET"})
     */
    public function index(PlanIndicatorsRepository $planIndicatorsRepository): Response
    {
        $planIndicator = $planIndicatorsRepository->findOneBy([]);
        if (empty($planIndicator)) {
           return $this->redirectToRoute('plan_indicators_new');
        } else {
            return $this->redirectToRoute('plan_indicators_show', ['id' => $planIndicator->getId()]);
        }
    }

    /**
     * @Route("/new", name="plan_indicators_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planIndicator = new PlanIndicators();
        $form = $this->createForm(PlanIndicatorsType::class, $planIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planIndicator);
            $entityManager->flush();

            return $this->redirectToRoute('plan_indicators_index');
        }

        return $this->render('plan_indicators/new.html.twig', [
            'plan_indicator' => $planIndicator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_indicators_show", methods={"GET"})
     */
    public function show(PlanIndicators $planIndicator): Response
    {
        return $this->render('plan_indicators/show.html.twig', [
            'plan_indicator' => $planIndicator,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plan_indicators_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlanIndicators $planIndicator): Response
    {
        $form = $this->createForm(PlanIndicatorsType::class, $planIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plan_indicators_index');
        }

        return $this->render('plan_indicators/edit.html.twig', [
            'plan_indicator' => $planIndicator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_indicators_delete", methods={"POST"})
     */
    public function delete(Request $request, PlanIndicators $planIndicator): Response
    {
        if ($this->isCsrfTokenValid('delete' . $planIndicator->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planIndicator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_indicators_index');
    }
}
