<?php

namespace App\Controller\Production;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use App\Entity\Supplier;
use App\Form\InputDeliveryProductionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
Use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionInputsController extends AbstractController
{
    /**
     * @Route("/inputs", name="production_inputs_index")
     */
    public function inputsSite(): Response
    {
        $inputs = $this->inputsList();

        return $this->render('input_delivery/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/inputs/breeder/{id}", name="production_inputs_breeder", methods={"GET"})
     */
    public function inputsBreeder(Inputs $inputs): Response
    {
        $breeders = $this->getDoctrine()->getRepository(Supplier::class)->findBy([], ['name' => 'asc']);

        return $this->render('input_delivery/production/breeder.html.twig', [
            'input' => $inputs,
            'breeders' => $breeders
        ]);
    }

    /**
     * @Route("/inputs/herd/{inputs}/{supplier}", name="production_inputs_herd", methods={"GET"})
     */
    public function inputsHerd(Inputs $inputs, Supplier $supplier): Response
    {
        $herds = $this->getDoctrine()->getRepository(Herds::class)->findBy(['breeder' => $supplier, 'active' => true]);

        return $this->render('input_delivery/production/herd.html.twig', [
            'input' => $inputs,
            'breeder' => $supplier,
            'herds' => $herds
        ]);
    }

    /**
     * @Route("/inputs/new/{herd}/{input}", name="production_inputs_new", methods={"GET", "POST"})
     */
    public function inputNew(Herds $herd, Inputs $input, Request $request)
    {
        $form = $this->createForm(InputDeliveryProductionType::class);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
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

            if ($form->get('saveBreeder')->isClicked()) {
                return $this->redirectToRoute('production_inputs_breeder', ['id' => $input->getId()]);
            }

            if ($form->get('saveHerd')->isClicked()) {
                return $this->redirectToRoute('production_inputs_herd', [
                    'inputs' => $input->getId(),
                    'supplier' => $herd->getBreeder()->getId()
                ]);
            }
            return $this->redirectToRoute('production_index');
        }

        return $this->render('input_delivery/production/new.html.twig', [
            'form' => $form->createView(),
            'herd' => $herd,
            'input' => $input
        ]);
    }

    public function inputsList()
    {
        $inputsRepository = $this->getDoctrine()->getRepository(Inputs::class);
        $date = new \DateTime();
        $date->modify('-22 days');
        return $inputsRepository->inputsInProduction($date);
    }

    public function herdDeliveryOnStock(Herds $herds)
    {
        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        return $deliveryRepository->herdDeliveryOnStock($herds);
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

}
