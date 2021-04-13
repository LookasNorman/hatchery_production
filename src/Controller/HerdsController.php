<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Form\HerdsType;
use App\Repository\EggSupplierRepository;
use App\Repository\HerdsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/herds")
 */
class HerdsController extends AbstractController
{
    /**
     * @Route("/", name="herds_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(HerdsRepository $herdsRepository): Response
    {
        return $this->render('herds/index.html.twig', [
            'herds' => $herdsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/breeder/{id}", name="herds_breeder_index", methods={"GET"})
     */
    public function breeder(HerdsRepository $herdsRepository, EggSupplierRepository $eggSupplierRepository, $id): Response
    {
        $breeder = $eggSupplierRepository->find($id);
        return $this->render('herds/index.html.twig', [
            'herds' => $herdsRepository->findBy(['breeder' => $breeder]),
            'breeder' => $breeder,
        ]);
    }

    /**
     * @Route("/new", name="herds_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $herd = new Herds();
        $form = $this->createForm(HerdsType::class, $herd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($herd);
            $entityManager->flush();

            return $this->redirectToRoute('herds_index');
        }

        return $this->render('herds/new.html.twig', [
            'herd' => $herd,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="herds_show", methods={"GET"})
     */
    public function show(Herds $herd): Response
    {
        return $this->render('herds/show.html.twig', [
            'herd' => $herd,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="herds_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Herds $herd): Response
    {
        $form = $this->createForm(HerdsType::class, $herd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('herds_index');
        }

        return $this->render('herds/edit.html.twig', [
            'herd' => $herd,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="herds_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Herds $herd): Response
    {
        if ($this->isCsrfTokenValid('delete'.$herd->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($herd);
            $entityManager->flush();
        }

        return $this->redirectToRoute('herds_index');
    }
}
