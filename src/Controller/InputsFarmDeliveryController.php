<?php

namespace App\Controller;

use App\Entity\InputsFarm;
use App\Entity\InputsFarmDelivery;
use App\Form\InputsFarmDeliveryType;
use App\Repository\DeliveryRepository;
use App\Repository\InputsFarmDeliveryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inputs_farm_delivery")
 */
class InputsFarmDeliveryController extends AbstractController
{
    /**
     * @Route("/", name="inputs_farm_delivery_index", methods={"GET"})
     */
    public function index(InputsFarmDeliveryRepository $inputsFarmDeliveryRepository): Response
    {
        return $this->render('inputs_farm_delivery/index.html.twig', [
            'inputs_farm_deliveries' => $inputsFarmDeliveryRepository->findAll(),
        ]);
    }

    public function list(InputsFarmDeliveryRepository $inputsFarmDeliveryRepository, InputsFarm $farm): Response
    {
        return $this->render('inputs_farm_delivery/index.html.twig', [
            'inputs_farm_deliveries' => $inputsFarmDeliveryRepository->findBy(['inputsFarm' => $farm]),
        ]);
    }

    public function addDelivery($deliveries, $totalEggs, $inputFarm)
    {
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($deliveries as $delivery) {

            $eggsNumber = $delivery->getEggsOnWarehouse();

            if ($totalEggs > 0 && $eggsNumber > 0) {
                $inputsFarmDelivery = new InputsFarmDelivery();
                $inputsFarmDelivery->setDelivery($delivery);
                $inputsFarmDelivery->setInputsFarm($inputFarm);
                if ($eggsNumber >= $totalEggs) {
                    $inputsFarmDelivery->setEggsNumber($totalEggs);
                    $delivery->setEggsOnWarehouse($eggsNumber - $totalEggs);
                    $totalEggs = 0;
                } else {
                    $inputsFarmDelivery->setEggsNumber($eggsNumber);
                    $delivery->setEggsOnWarehouse(0);
                    $totalEggs = $totalEggs - $eggsNumber;
                }
                $entityManager->persist($inputsFarmDelivery);
            }
        }
        $entityManager->flush();
    }

    public function getEggsNumberInDeliveries($deliveries)
    {
        $eggsNumber = 0;
        foreach ($deliveries as $delivery) {
            $eggsNumber = $eggsNumber + $delivery->getEggsNumber();
        }
        return $eggsNumber;
    }

    /**
     * @Route("/new/{id}", name="inputs_farm_delivery_new", methods={"GET","POST"})
     */
    public function new(Request $request, InputsFarm $farm, DeliveryRepository $deliveryRepository): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $inputsFarmDelivery = new InputsFarmDelivery();
        $inputsFarmDelivery->setInputsFarm($farm);
        $form = $this->createForm(InputsFarmDeliveryType::class, $inputsFarmDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $herd = $form['herd']->getData();
            $totalEggs = $form['eggsNumber']->getData();
            $deliveries = $deliveryRepository->eggsOnWarehouse($herd);

            $eggsNumber = $this->getEggsNumberInDeliveries($deliveries);

            if ($eggsNumber >= $totalEggs) {
                $this->addDelivery($deliveries, $totalEggs, $farm);
                return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputsFarmDelivery->getInputsFarm()->getEggInput()->getId()]);
            }
        }


        return $this->render('inputs_farm_delivery/new.html.twig', [
            'inputs_farm_delivery' => $inputsFarmDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="inputs_farm_delivery_show", methods={"GET"})
     */
    public function show(InputsFarmDelivery $inputsFarmDelivery): Response
    {
        return $this->render('inputs_farm_delivery/show.html.twig', [
            'inputs_farm_delivery' => $inputsFarmDelivery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="inputs_farm_delivery_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, InputsFarmDelivery $inputsFarmDelivery): Response
    {
        $form = $this->createForm(InputsFarmDeliveryType::class, $inputsFarmDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('inputs_farm_delivery_index');
        }

        return $this->render('inputs_farm_delivery/edit.html.twig', [
            'inputs_farm_delivery' => $inputsFarmDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="inputs_farm_delivery_delete", methods={"POST"})
     */
    public function delete(Request $request, InputsFarmDelivery $inputsFarmDelivery): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inputsFarmDelivery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inputsFarmDelivery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('inputs_farm_delivery_index');
    }
}
