<?php

namespace App\Controller;

use App\Entity\Setters;
use App\Form\SettersType;
use App\Repository\SettersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/setters")
 */
class SettersController extends AbstractController
{
    /**
     * @Route("/", name="setters_index", methods={"GET"})
     */
    public function index(SettersRepository $settersRepository): Response
    {
        return $this->render('setters/index.html.twig', [
            'setters' => $settersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="setters_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $setter = new Setters();
        $form = $this->createForm(SettersType::class, $setter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($setter);
            $entityManager->flush();

            return $this->redirectToRoute('setters_index');
        }

        return $this->render('setters/new.html.twig', [
            'setter' => $setter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="setters_show", methods={"GET"})
     */
    public function show(Setters $setter): Response
    {
        return $this->render('setters/show.html.twig', [
            'setter' => $setter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="setters_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Setters $setter): Response
    {
        $form = $this->createForm(SettersType::class, $setter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('setters_index');
        }

        return $this->render('setters/edit.html.twig', [
            'setter' => $setter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="setters_delete", methods={"POST"})
     */
    public function delete(Request $request, Setters $setter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$setter->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($setter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('setters_index');
    }
}
