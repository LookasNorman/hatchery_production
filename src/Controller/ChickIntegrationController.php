<?php

namespace App\Controller;

use App\Entity\ChickIntegration;
use App\Entity\ChicksRecipient;
use App\Entity\Customer;
use App\Form\ChickIntegrationChooseType;
use App\Form\ChickIntegrationType;
use App\Repository\ChickIntegrationRepository;
use App\Repository\PlanDeliveryChickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chick_integration")
 * @IsGranted("ROLE_USER")
 */
class ChickIntegrationController extends AbstractController
{
    /**
     * @Route("/", name="chick_integration_index", methods={"GET"})
     */
    public function index(ChickIntegrationRepository $chickIntegrationRepository): Response
    {
        return $this->render('chick_integration/index.html.twig', [
            'chick_integrations' => $chickIntegrationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="chick_integration_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function new(Request $request): Response
    {
        $chickIntegration = new ChickIntegration();
        $form = $this->createForm(ChickIntegrationType::class, $chickIntegration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chickIntegration);
            $entityManager->flush();

            return $this->redirectToRoute('chick_integration_index');
        }

        return $this->render('chick_integration/new.html.twig', [
            'chick_integration' => $chickIntegration,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/choose_integration", name="chick_integration_choose_integration", methods={"GET", "POST"})
     */
    public function chooseIntegration(Request $request)
    {
        $form = $this->createForm(ChickIntegrationChooseType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            return $this->redirectToRoute('chick_integration_monthly_plan', [
                'id' => $form->get('chickIntegration')->getData()->getId()
            ]);
        }
        return $this->render('chick_integration/choose_integration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="chick_integration_show", methods={"GET"})
     */
    public function show(ChickIntegration $chickIntegration): Response
    {
        $customers = $this->getDoctrine()->getRepository(Customer::class)->customersIntegrationWithPlan($chickIntegration);
        $chicks_recipients = $this->getDoctrine()->getRepository(ChicksRecipient::class)->chickRecipientsIntegrationWithPlan($chickIntegration);

        return $this->render('chick_integration/show.html.twig', [
            'chick_integration' => $chickIntegration,
            'customers' => $customers,
            'chicks_recipients' => $chicks_recipients
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chick_integration_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function edit(Request $request, ChickIntegration $chickIntegration): Response
    {
        $form = $this->createForm(ChickIntegrationType::class, $chickIntegration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('chick_integration_index');
        }

        return $this->render('chick_integration/edit.html.twig', [
            'chick_integration' => $chickIntegration,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chick_integration_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, ChickIntegration $chickIntegration): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chickIntegration->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chickIntegration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chick_integration_index');
    }

    /**
     * @Route("/{id}/monthly_plan", name="chick_integration_monthly_plan", methods={"GET"})
     */
    public function showMonthlyPlan(ChickIntegration $chickIntegration, PlanDeliveryChickRepository $planDeliveryChickRepository)
    {
        $plans = $planDeliveryChickRepository->monthlyIntegrationPlan($chickIntegration);
        return $this->render('chick_integration/monthly_plan.html.twig', [
            'plans' => $plans,
            'integration' => $chickIntegration
        ]);
    }
}
