<?php

namespace App\Controller;

use App\Entity\InputsFarm;
use App\Entity\InputsFarmDelivery;
use App\Form\InputsFarmDeliveryType;
use App\Repository\DeliveryRepository;
use App\Repository\InputsFarmDeliveryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inputs_farm_delivery")
 * @IsGranted("ROLE_USER")
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

    public function eggsOnWareHouseInDelivery($delivery)
    {
        $inputsEggs = $delivery->getInputsFarmDeliveries();
        $eggsInput = 0;
        foreach ($inputsEggs as $inputEggs) {
            $eggsInput = $eggsInput + $inputEggs->getEggsNumber();
        }
        $eggsOnWarehouse = $delivery->getEggsNumber() - $eggsInput;
        return $eggsOnWarehouse;
    }

    public function getEggsNumberInDeliveries($deliveries)
    {
        $eggsNumber = 0;
        foreach ($deliveries as $delivery) {
            $eggsNumber = $eggsNumber + $delivery['eggsOnWarehouse'];
        }
        return $eggsNumber;
    }

    /**
     * @Route("/new/{id}", name="inputs_farm_delivery_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function new(
        Request                      $request,
        InputsFarm                   $farm,
        DeliveryRepository           $deliveryRepository,
        InputsFarmDeliveryRepository $inputsFarmDeliveryRepository
    ): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(InputsFarmDeliveryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $herd = $form['herd']->getData();
            $totalEggs = $form['eggsNumber']->getData();
            $deliveries = [];
            $eggsDeliveries = $deliveryRepository->findBy(['herd' => $herd], ['deliveryDate' => 'ASC']);
            foreach ($eggsDeliveries as $eggsDelivery) {
                $eggsInputs = $inputsFarmDeliveryRepository->eggsFromDelivery($eggsDelivery);
                $eggsOnWarehouse = $this->eggsOnWareHouseInDelivery($eggsDelivery);
                if ($eggsInputs > 0 or is_null($eggsInputs)) {
                    array_push($deliveries, ['delivery' => $eggsDelivery, 'eggsOnWarehouse' => $eggsOnWarehouse]);
                }
            }
            $eggsNumber = $this->getEggsNumberInDeliveries($deliveries);

            if ($eggsNumber >= $totalEggs) {
                foreach ($deliveries as $delivery) {
                    $inputsFarmDelivery = new InputsFarmDelivery();
                    $inputsFarmDelivery->setInputsFarm($farm);
                    if ($totalEggs > 0) {
                        if ($totalEggs > $delivery['eggsOnWarehouse']) {

                            $inputsFarmDelivery->setDelivery($delivery['delivery']);
                            $inputsFarmDelivery->setEggsNumber($delivery['eggsOnWarehouse']);
                            $totalEggs = $totalEggs - (int)$delivery['eggsOnWarehouse'];
                        } else {
                            $inputsFarmDelivery->setDelivery($delivery['delivery']);
                            $inputsFarmDelivery->setEggsNumber($totalEggs);
                            $totalEggs = 0;
                        }
                        $entityManager->persist($inputsFarmDelivery);
                    }
                }
                $entityManager->flush();
                return $this->redirectToRoute('eggs_inputs_show', ['id' => $farm->getEggInput()->getId()]);
            }
        }


        return $this->render('inputs_farm_delivery/new.html.twig', [
            'farm' => $farm,
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
     * @IsGranted("ROLE_MANAGER")
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
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, InputsFarmDelivery $inputsFarmDelivery): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inputsFarmDelivery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inputsFarmDelivery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputsFarmDelivery->getInputsFarm()->getEggInput()->getId()]);
    }
}
