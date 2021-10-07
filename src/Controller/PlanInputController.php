<?php

namespace App\Controller;

use App\Entity\PlanInput;
use App\Form\PlanInputType;
use App\Repository\PlanInputFarmRepository;
use App\Repository\PlanInputRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_input")
 */
class PlanInputController extends AbstractController
{
    /**
     * @Route("/", name="plan_input_index", methods={"GET"})
     */
    public function index(PlanInputRepository $planInputRepository): Response
    {
        return $this->render('plan_input/index.html.twig', [
            'plan_inputs' => $planInputRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="plan_input_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planInput = new PlanInput();
        $form = $this->createForm(PlanInputType::class, $planInput);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planInput);
            $entityManager->flush();

            return $this->redirectToRoute('plan_input_index');
        }

        return $this->render('plan_input/new.html.twig', [
            'plan_input' => $planInput,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_input_show", methods={"GET"})
     */
    public function show(
        PlanInput $planInput,
        PlanInputFarmRepository $farmRepository
    ): Response
    {
        $farms = $farmRepository->findBy(['eggInput' => $planInput]);

        return $this->render('plan_input/show.html.twig', [
            'plan_input' => $planInput,
            'farms' => $farms
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plan_input_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlanInput $planInput): Response
    {
        $form = $this->createForm(PlanInputType::class, $planInput);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plan_input_index');
        }

        return $this->render('plan_input/edit.html.twig', [
            'plan_input' => $planInput,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_input_delete", methods={"POST"})
     */
    public function delete(Request $request, PlanInput $planInput): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planInput->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planInput);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_input_index');
    }
}
