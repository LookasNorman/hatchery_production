<?php

namespace App\Controller;

use App\Entity\EggsInputsLighting;
use App\Form\EggsInputsLightingType;
use App\Repository\EggsInputsLightingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_inputs_lighting")
 * @IsGranted("ROLE_USER")
 */
class EggsInputsLightingController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_lighting_index", methods={"GET"})
     */
    public function index(EggsInputsLightingRepository $eggsInputsLightingRepository): Response
    {
        return $this->render('eggs_inputs_lighting/index.html.twig', [
            'eggs_inputs_lightings' => $eggsInputsLightingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_inputs_lighting_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsInputsLighting = new EggsInputsLighting();
        $form = $this->createForm(EggsInputsLightingType::class, $eggsInputsLighting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eggsInputsLighting);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_lighting_index');
        }

        return $this->render('eggs_inputs_lighting/new.html.twig', [
            'eggs_inputs_lighting' => $eggsInputsLighting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_lighting_show", methods={"GET"})
     */
    public function show(EggsInputsLighting $eggsInputsLighting): Response
    {
        return $this->render('eggs_inputs_lighting/show.html.twig', [
            'eggs_inputs_lighting' => $eggsInputsLighting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_lighting_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsInputsLighting $eggsInputsLighting): Response
    {
        $form = $this->createForm(EggsInputsLightingType::class, $eggsInputsLighting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_lighting_index');
        }

        return $this->render('eggs_inputs_lighting/edit.html.twig', [
            'eggs_inputs_lighting' => $eggsInputsLighting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_lighting_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggsInputsLighting $eggsInputsLighting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggsInputsLighting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsLighting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_lighting_index');
    }
}
