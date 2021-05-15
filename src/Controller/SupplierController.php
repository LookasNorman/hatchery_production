<?php

namespace App\Controller;

use App\Entity\EggSupplier;
use App\Form\EggSupplierType;
use App\Repository\EggSupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/supplier")
 * @IsGranted("ROLE_USER")
 */
class SupplierController extends AbstractController
{
    /**
     * @Route("/", name="egg_supplier_index", methods={"GET"})
     */
    public function index(EggSupplierRepository $eggSupplierRepository): Response
    {
        return $this->render('egg_supplier/index.html.twig', [
            'egg_suppliers' => $eggSupplierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="egg_supplier_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggSupplier = new EggSupplier();
        $form = $this->createForm(EggSupplierType::class, $eggSupplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eggSupplier);
            $entityManager->flush();

            return $this->redirectToRoute('egg_supplier_index');
        }

        return $this->render('egg_supplier/new.html.twig', [
            'egg_supplier' => $eggSupplier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="egg_supplier_show", methods={"GET"})
     */
    public function show(EggSupplier $eggSupplier): Response
    {
        return $this->render('egg_supplier/show.html.twig', [
            'egg_supplier' => $eggSupplier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="egg_supplier_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggSupplier $eggSupplier): Response
    {
        $form = $this->createForm(EggSupplierType::class, $eggSupplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('egg_supplier_index');
        }

        return $this->render('egg_supplier/edit.html.twig', [
            'egg_supplier' => $eggSupplier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="egg_supplier_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggSupplier $eggSupplier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggSupplier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggSupplier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('egg_supplier_index');
    }
}
