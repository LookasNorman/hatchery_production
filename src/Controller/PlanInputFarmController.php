<?php

namespace App\Controller;

use App\Entity\PlanInput;
use App\Entity\PlanInputFarm;
use App\Form\PlanInputFarmType;
use App\Repository\PlanInputFarmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_input_farm")
 */
class PlanInputFarmController extends AbstractController
{

    /**
     * @Route("/new/{id}", name="plan_input_farm_new", methods={"GET","POST"})
     */
    public function new(Request $request, PlanInput $planInput): Response
    {
        $planInputFarm = new PlanInputFarm();
        $planInputFarm->setEggInput($planInput);
        $form = $this->createForm(PlanInputFarmType::class, $planInputFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planInputFarm);
            $entityManager->flush();

            return $this->redirectToRoute('plan_input_show', ['id' => $planInput->getId()]);
        }

        return $this->render('plan_input_farm/new.html.twig', [
            'plan_input_farm' => $planInputFarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_input_farm_delete", methods={"POST"})
     */
    public function delete(Request $request, PlanInputFarm $planInputFarm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planInputFarm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planInputFarm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_input_farm_index');
    }
}
