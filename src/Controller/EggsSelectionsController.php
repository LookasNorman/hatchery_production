<?php

namespace App\Controller;

use App\Entity\EggsSelections;
use App\Form\EggsSelectionsType;
use App\Repository\EggsSelectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_selections")
 * @IsGranted("ROLE_USER")
 */
class EggsSelectionsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_selections_index", methods={"GET"})
     */
    public function index(EggsSelectionsRepository $eggsSelectionsRepository): Response
    {
        return $this->render('eggs_selections/index.html.twig', [
            'eggs_selections' => $eggsSelectionsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_selections_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsSelection = new EggsSelections();
        $form = $this->createForm(EggsSelectionsType::class, $eggsSelection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eggsSelection);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_selections_index');
        }

        return $this->render('eggs_selections/new.html.twig', [
            'eggs_selection' => $eggsSelection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_selections_show", methods={"GET"})
     */
    public function show(EggsSelections $eggsSelection): Response
    {
        return $this->render('eggs_selections/show.html.twig', [
            'eggs_selection' => $eggsSelection,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_selections_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsSelections $eggsSelection): Response
    {
        $form = $this->createForm(EggsSelectionsType::class, $eggsSelection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_selections_index');
        }

        return $this->render('eggs_selections/edit.html.twig', [
            'eggs_selection' => $eggsSelection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_selections_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggsSelections $eggsSelection): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggsSelection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsSelection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_selections_index');
    }
}
