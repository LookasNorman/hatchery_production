<?php

namespace App\Controller;

use App\Entity\SellingEgg;
use App\Form\SellingEggType;
use App\Repository\SellingEggRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/selling_egg")
 * @IsGranted("ROLE_USER")
 */
class SellingEggController extends AbstractController
{
    /**
     * @Route("/", name="selling_egg_index", methods={"GET"})
     */
    public function index(SellingEggRepository $sellingEggRepository): Response
    {
        return $this->render('selling_egg/index.html.twig', [
            'selling_eggs' => $sellingEggRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="selling_egg_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sellingEgg = new SellingEgg();
        $form = $this->createForm(SellingEggType::class, $sellingEgg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sellingEgg);
            $entityManager->flush();

            return $this->redirectToRoute('selling_egg_index');
        }

        return $this->render('selling_egg/new.html.twig', [
            'selling_egg' => $sellingEgg,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="selling_egg_show", methods={"GET"})
     */
    public function show(SellingEgg $sellingEgg): Response
    {
        return $this->render('selling_egg/show.html.twig', [
            'selling_egg' => $sellingEgg,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="selling_egg_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SellingEgg $sellingEgg): Response
    {
        $form = $this->createForm(SellingEggType::class, $sellingEgg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('selling_egg_index');
        }

        return $this->render('selling_egg/edit.html.twig', [
            'selling_egg' => $sellingEgg,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="selling_egg_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, SellingEgg $sellingEgg): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sellingEgg->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sellingEgg);
            $entityManager->flush();
        }

        return $this->redirectToRoute('selling_egg_index');
    }
}
