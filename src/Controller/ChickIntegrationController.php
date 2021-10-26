<?php

namespace App\Controller;

use App\Entity\ChickIntegration;
use App\Form\ChickIntegrationType;
use App\Repository\ChickIntegrationRepository;
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
     * @Route("/{id}", name="chick_integration_show", methods={"GET"})
     */
    public function show(ChickIntegration $chickIntegration): Response
    {
        return $this->render('chick_integration/show.html.twig', [
            'chick_integration' => $chickIntegration,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chick_integration_edit", methods={"GET","POST"})
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
}
