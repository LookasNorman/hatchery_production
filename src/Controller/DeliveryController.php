<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Form\DeliveryPartIndexType;
use App\Form\DeliveryType;
use App\Repository\EggsDeliveryRepository;
use App\Repository\EggsInputsDetailsEggsDeliveryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/delivery")
 * @IsGranted("ROLE_USER")
 */
class DeliveryController extends AbstractController
{
    /**
     * @Route("/", name="eggs_delivery_index", methods={"GET"})
     */
    public function index(EggsDeliveryRepository $eggsDeliveryRepository): Response
    {
        return $this->render('eggs_delivery/index.html.twig', [
            'eggs_deliveries' => $eggsDeliveryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_delivery_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsDelivery = new Delivery();
        $form = $this->createForm(DeliveryType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $eggsDelivery->setEggsOnWarehouse($eggsDelivery->getEggsNumber());
            $entityManager->persist($eggsDelivery);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_delivery_show", methods={"GET"})
     */
    public function show(Delivery $eggsDelivery): Response
    {
        return $this->render('eggs_delivery/show.html.twig', [
            'eggs_delivery' => $eggsDelivery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_delivery_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Delivery $eggsDelivery): Response
    {
        $form = $this->createForm(DeliveryType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/edit.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/add", name="eggs_delivery_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addPartIndex(Request $request, Delivery $eggsDelivery)
    {
        $form = $this->createForm(DeliveryPartIndexType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/addPartIndex.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_delivery_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Delivery $eggsDelivery): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggsDelivery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsDelivery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_delivery_index');
    }
}
