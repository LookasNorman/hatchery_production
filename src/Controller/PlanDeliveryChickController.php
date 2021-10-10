<?php

namespace App\Controller;

use App\Entity\PlanDeliveryChick;
use App\Form\PlanDeliveryChickType;
use App\Repository\PlanDeliveryChickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_delivery_chick")
 */
class PlanDeliveryChickController extends AbstractController
{
    /**
     * @Route("/", name="plan_delivery_chick_index", methods={"GET"})
     */
    public function index(PlanDeliveryChickRepository $planDeliveryChickRepository): Response
    {
        return $this->render('plan_delivery_chick/index.html.twig', [
            'plan_delivery_chicks' => $planDeliveryChickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="plan_delivery_chick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planDeliveryChick = new PlanDeliveryChick();
        $form = $this->createForm(PlanDeliveryChickType::class, $planDeliveryChick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputDate = clone $planDeliveryChick->getDeliveryDate();
            $lightingDate = clone $planDeliveryChick->getDeliveryDate();
            $transferDate = clone $planDeliveryChick->getDeliveryDate();
            $planDeliveryChick->setInputDate($inputDate->sub(new \DateInterval('P21DT5H')));
            $planDeliveryChick->setLightingDate($lightingDate->sub(new \DateInterval('P6DT21H')));
            $planDeliveryChick->setTransferDate($transferDate->sub(new \DateInterval('P2DT17H')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planDeliveryChick);
            $entityManager->flush();

            return $this->redirectToRoute('plan_delivery_chick_index');
        }

        return $this->render('plan_delivery_chick/new.html.twig', [
            'plan_delivery_chick' => $planDeliveryChick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_delivery_chick_show", methods={"GET"})
     */
    public function show(PlanDeliveryChick $planDeliveryChick): Response
    {
        return $this->render('plan_delivery_chick/show.html.twig', [
            'plan_delivery_chick' => $planDeliveryChick,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plan_delivery_chick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlanDeliveryChick $planDeliveryChick): Response
    {
        $form = $this->createForm(PlanDeliveryChickType::class, $planDeliveryChick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plan_delivery_chick_index');
        }

        return $this->render('plan_delivery_chick/edit.html.twig', [
            'plan_delivery_chick' => $planDeliveryChick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_delivery_chick_delete", methods={"POST"})
     */
    public function delete(Request $request, PlanDeliveryChick $planDeliveryChick): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planDeliveryChick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planDeliveryChick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_delivery_chick_index');
    }
}
