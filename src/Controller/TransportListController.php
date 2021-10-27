<?php

namespace App\Controller;

use App\Entity\TransportList;
use App\Form\TransportListType;
use App\Repository\TransportListRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transport_list")
 * @IsGranted("ROLE_USER")
 */
class TransportListController extends AbstractController
{
    /**
     * @Route("/", name="transport_list_index", methods={"GET"})
     */
    public function index(TransportListRepository $transportListRepository): Response
    {
        return $this->render('transport_list/index.html.twig', [
            'transport_lists' => $transportListRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="transport_list_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $transportList = new TransportList();
        $form = $this->createForm(TransportListType::class, $transportList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transportList);
            $entityManager->flush();

            return $this->redirectToRoute('transport_list_index');
        }

        return $this->render('transport_list/new.html.twig', [
            'transport_list' => $transportList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_list_show", methods={"GET"})
     */
    public function show(TransportList $transportList): Response
    {
        return $this->render('transport_list/show.html.twig', [
            'transport_list' => $transportList,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transport_list_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TransportList $transportList): Response
    {
        $form = $this->createForm(TransportListType::class, $transportList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transport_list_index');
        }

        return $this->render('transport_list/edit.html.twig', [
            'transport_list' => $transportList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_list_delete", methods={"POST"})
     */
    public function delete(Request $request, TransportList $transportList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transportList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transportList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transport_list_index');
    }
}
