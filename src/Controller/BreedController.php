<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Form\BreedType;
use App\Repository\BreedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/breed")
 * @IsGranted('ROLE_USER')
 */
class BreedController extends AbstractController
{
    /**
     * @Route("/", name="breed_index", methods={"GET"})
     */
    public function index(BreedRepository $breedRepository): Response
    {
        return $this->render('breed/index.html.twig', [
            'breeds' => $breedRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="breed_new", methods={"GET","POST"})
     * @IsGranted('ROLE_ADMIN')
     */
    public function new(Request $request): Response
    {
        $breed = new Breed();
        $form = $this->createForm(BreedType::class, $breed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($breed);
            $entityManager->flush();

            return $this->redirectToRoute('breed_index');
        }

        return $this->render('breed/new.html.twig', [
            'breed' => $breed,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="breed_show", methods={"GET"})
     */
    public function show(Breed $breed): Response
    {
        return $this->render('breed/show.html.twig', [
            'breed' => $breed,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="breed_edit", methods={"GET","POST"})
     * @IsGranted('ROLE_ADMIN')
     */
    public function edit(Request $request, Breed $breed): Response
    {
        $form = $this->createForm(BreedType::class, $breed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('breed_index');
        }

        return $this->render('breed/edit.html.twig', [
            'breed' => $breed,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="breed_delete", methods={"POST"})
     * @IsGranted('ROLE_ADMIN')
     */
    public function delete(Request $request, Breed $breed): Response
    {
        if ($this->isCsrfTokenValid('delete'.$breed->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($breed);
            $entityManager->flush();
        }

        return $this->redirectToRoute('breed_index');
    }
}
