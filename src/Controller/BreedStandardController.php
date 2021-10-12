<?php

namespace App\Controller;

use App\Entity\BreedStandard;
use App\Form\BreedStandardType;
use App\Repository\BreedStandardRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/breed_standard")
 * @IsGranted("ROLE_USER")
 */
class BreedStandardController extends AbstractController
{
    /**
     * @Route("/", name="breed_standard_index", methods={"GET"})
     */
    public function index(BreedStandardRepository $breedStandardRepository): Response
    {
        return $this->render('breed_standard/index.html.twig', [
            'breed_standards' => $breedStandardRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="breed_standard_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $breedStandard = new BreedStandard();
        $form = $this->createForm(BreedStandardType::class, $breedStandard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($breedStandard);
            $entityManager->flush();

            return $this->redirectToRoute('breed_standard_index');
        }

        return $this->render('breed_standard/new.html.twig', [
            'breed_standard' => $breedStandard,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="breed_standard_show", methods={"GET"})
     */
    public function show(BreedStandard $breedStandard): Response
    {
        return $this->render('breed_standard/show.html.twig', [
            'breed_standard' => $breedStandard,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="breed_standard_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BreedStandard $breedStandard): Response
    {
        $form = $this->createForm(BreedStandardType::class, $breedStandard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('breed_standard_index');
        }

        return $this->render('breed_standard/edit.html.twig', [
            'breed_standard' => $breedStandard,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="breed_standard_delete", methods={"POST"})
     */
    public function delete(Request $request, BreedStandard $breedStandard): Response
    {
        if ($this->isCsrfTokenValid('delete'.$breedStandard->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($breedStandard);
            $entityManager->flush();
        }

        return $this->redirectToRoute('breed_standard_index');
    }
}
