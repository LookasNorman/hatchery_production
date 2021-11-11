<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\InputsFarmDelivery;
use App\Entity\InputsFarmDeliveryPlan;
use App\Form\InputsFarmDeliveryType;
use App\Repository\DeliveryRepository;
use App\Repository\InputsFarmDeliveryPlanRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\InputsFarmRepository;
use App\Repository\InputsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inputs_farm_delivery_plan")
 * @IsGranted("ROLE_USER")
 */
class InputsFarmDeliveryPlanController extends AbstractController
{
    /**
     * @Route("/", name="inputs_farm_delivery_plan_index", methods={"GET"})
     */
    public function index(InputsRepository $inputsRepository): Response
    {
        $date = new \DateTime();
        $date->modify('-1 days');
        $inputs = $inputsRepository->inputsInProduction($date);

        return $this->render('inputs_farm_delivery/plan/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/inputs/{id}", name="eggs_inputs_plan_show", methods={"GET"})
     */
    public function inputsShow(
        Inputs                       $eggsInput,
        InputsFarmRepository         $farmRepository
    ): Response
    {
        $farms = $farmRepository->findBy(['eggInput' => $eggsInput]);

        return $this->render('eggs_inputs/plan/show.html.twig', [
            'eggs_input' => $eggsInput,
            'farms' => $farms,
        ]);
    }

    public function list(InputsFarmDeliveryPlanRepository $inputsFarmDeliveryPlanRepository, InputsFarm $farm): Response
    {
        return $this->render('inputs_farm_delivery/index.html.twig', [
            'inputs_farm_deliveries' => $inputsFarmDeliveryPlanRepository->findBy(['inputsFarm' => $farm]),
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
     * @Route("/new/{id}", name="inputs_farm_delivery_plan_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function new(
        Request                          $request,
        InputsFarm                       $farm,
        DeliveryRepository               $deliveryRepository,
        InputsFarmDeliveryPlanRepository $inputsFarmDeliveryPlanRepository
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
                $eggsInputs = $inputsFarmDeliveryPlanRepository->eggsFromDelivery($eggsDelivery);
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
                            dump($delivery['eggsOnWarehouse']);
                            $totalEggs = $totalEggs - (int)$delivery['eggsOnWarehouse'];
                        } else {
                            $inputsFarmDelivery->setDelivery($delivery['delivery']);
                            $inputsFarmDelivery->setEggsNumber($totalEggs);
                            $totalEggs = 0;
                        }
                    }
                    $entityManager->persist($inputsFarmDelivery);
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
     * @Route("/{id}", name="inputs_farm_delivery_plan_show", methods={"GET"})
     */
    public function show(InputsFarmDeliveryPlan $inputsFarmDeliveryPlan): Response
    {
        return $this->render('inputs_farm_delivery/show.html.twig', [
            'inputs_farm_delivery' => $inputsFarmDeliveryPlan,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="inputs_farm_delivery_plan_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function edit(Request $request, InputsFarmDeliveryPlan $inputsFarmDeliveryPlan): Response
    {
        $form = $this->createForm(InputsFarmDeliveryType::class, $inputsFarmDeliveryPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('inputs_farm_delivery_index');
        }

        return $this->render('inputs_farm_delivery/edit.html.twig', [
            'inputs_farm_delivery' => $inputsFarmDeliveryPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="inputs_farm_delivery_plan_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, InputsFarmDeliveryPlan $inputsFarmDeliveryPlan): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inputsFarmDeliveryPlan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inputsFarmDeliveryPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputsFarmDeliveryPlan->getInputsFarm()->getEggInput()->getId()]);
    }

}
