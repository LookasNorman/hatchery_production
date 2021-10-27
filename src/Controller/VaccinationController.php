<?php

namespace App\Controller;

use App\Entity\Vaccination;
use App\Form\VaccinationType;
use App\Repository\VaccinationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vaccination")
 */
class VaccinationController extends AbstractController
{
    /**
     * @Route("/", name="vaccination_index", methods={"GET"})
     */
    public function index(VaccinationRepository $vaccinationRepository): Response
    {
        return $this->render('vaccination/index.html.twig', [
            'vaccinations' => $vaccinationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="vaccination_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $vaccination = new Vaccination();
        $form = $this->createForm(VaccinationType::class, $vaccination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vaccination);
            $entityManager->flush();

            return $this->redirectToRoute('vaccination_index');
        }

        return $this->render('vaccination/new.html.twig', [
            'vaccination' => $vaccination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vaccination_show", methods={"GET"})
     */
    public function show(Vaccination $vaccination): Response
    {
        return $this->render('vaccination/show.html.twig', [
            'vaccination' => $vaccination,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vaccination_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vaccination $vaccination): Response
    {
        $form = $this->createForm(VaccinationType::class, $vaccination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vaccination_index');
        }

        return $this->render('vaccination/edit.html.twig', [
            'vaccination' => $vaccination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vaccination_delete", methods={"POST"})
     */
    public function delete(Request $request, Vaccination $vaccination): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vaccination->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vaccination);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vaccination_index');
    }
}
