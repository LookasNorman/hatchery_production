<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use App\Form\InputDeliveryType;
use App\Repository\InputDeliveryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/input_delivery")
 * @IsGranted("ROLE_USER")
 */
class InputDeliveryController extends AbstractController
{
    /**
     * @Route("/", name="input_delivery_index", methods={"GET"})
     */
    public function index(InputDeliveryRepository $inputDeliveryRepository): Response
    {
        return $this->render('input_delivery/index.html.twig', [
            'input_deliveries' => $inputDeliveryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/herd/{inputs}/{herds}", name="input_delivery_herd", methods={"GET"})
     */
    public function herd(Inputs $inputs, Herds $herds, InputDeliveryRepository $inputDeliveryRepository): Response
    {
        return $this->render('input_delivery/index.html.twig', [
            'input_deliveries' => $inputDeliveryRepository->inputHerd($inputs, $herds),
        ]);
    }

    public function herdDeliveryOnStock(Herds $herds)
    {
        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        $deliveries = $deliveryRepository->herdDeliveryOnStock($herds);
        return $deliveries;
    }

    public function checkStockEggs(Delivery $delivery)
    {
        $deliveryEggs = $delivery->getEggsNumber();
        $inputEggs = 0;
        foreach ($delivery->getInputDeliveries() as $inputDelivery) {
            $inputEggs = $inputEggs + $inputDelivery->getEggsNumber();
        }
        $sellingEggs = 0;
        foreach ($delivery->getSellingEggs() as $sellingEgg) {
            $sellingEggs = $sellingEggs + $sellingEgg->getEggsNumber();
        }
        return $deliveryEggs - $inputEggs - $sellingEggs;
    }

    /**
     * @Route("/new", name="input_delivery_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(InputDeliveryType::class);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $herd = $form['herd']->getData();
            $input = $form['input']->getData();
            $totalEggs = $form['eggs']->getData();
            $deliveries = $this->herdDeliveryOnStock($herd);
            foreach ($deliveries as $delivery) {
                $eggsNumber = $this->checkStockEggs($delivery);
                if ($totalEggs > 0) {
                    $inputDelivery = new InputDelivery();
                    if ($eggsNumber > 0) {
                        $inputDelivery->setDelivery($delivery);
                        $inputDelivery->setInput($input);
                        if ($eggsNumber >= $totalEggs) {
                            $inputDelivery->setEggsNumber($totalEggs);
                            $totalEggs = 0;
                        } else {
                            $inputDelivery->setEggsNumber($eggsNumber);
                            $totalEggs = $totalEggs - $eggsNumber;
                        }
                        $entityManager->persist($inputDelivery);
                    }
                }
            }
            $entityManager->flush();
            return $this->redirectToRoute('input_delivery_index');
        }

        return $this->render('input_delivery/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="input_delivery_show", methods={"GET"})
     */
    public function show(InputDelivery $inputDelivery): Response
    {
        return $this->render('input_delivery/show.html.twig', [
            'input_delivery' => $inputDelivery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="input_delivery_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, InputDelivery $inputDelivery): Response
    {
        $form = $this->createForm(InputDeliveryType::class, $inputDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('input_delivery_index');
        }

        return $this->render('input_delivery/edit.html.twig', [
            'input_delivery' => $inputDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="input_delivery_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, InputDelivery $inputDelivery): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inputDelivery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inputDelivery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('input_delivery_index');
    }
}
