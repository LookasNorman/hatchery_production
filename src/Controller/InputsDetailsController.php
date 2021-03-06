<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\DetailsDelivery;
use App\Entity\Herds;
use App\Form\InputsDetailsType;
use App\Repository\DeliveryRepository;
use App\Repository\DetailsDeliveryRepository;
use App\Repository\DetailsRepository;
use App\Repository\InputsRepository;
use App\Repository\HerdsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/inputs_details")
 * @IsGranted("ROLE_USER")
 */
class InputsDetailsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_details_index", methods={"GET"})
     */
    public function index(DetailsRepository $eggsInputsDetailsRepository, DetailsDeliveryRepository $repository): Response
    {
        $eggsInputsDetails = $eggsInputsDetailsRepository->deliveries();
        return $this->render('eggs_inputs_details/index.html.twig', [
            'eggs_inputs_details' => $eggsInputsDetails,
        ]);
    }

    public function addDelivery($deliveries, $totalEggs, $eggsInputsDetail)
    {
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($deliveries as $delivery) {
            $eggsNumber = $delivery->getEggsOnWarehouse();
            if ($totalEggs > 0 && $eggsNumber > 0) {
                $eggsInputsDetailEggsDeliveries = new DetailsDelivery();
                $eggsInputsDetailEggsDeliveries->setEggsDeliveries($delivery);
                $eggsInputsDetailEggsDeliveries->setEggsInputDetails($eggsInputsDetail);
                if ($eggsNumber >= $totalEggs) {
                    $eggsInputsDetailEggsDeliveries->setEggsNumber($totalEggs);
                    $delivery->setEggsOnWarehouse($eggsNumber - $totalEggs);
                    $totalEggs = 0;
                } else {
                    $eggsInputsDetailEggsDeliveries->setEggsNumber($eggsNumber);
                    $delivery->setEggsOnWarehouse(0);
                    $totalEggs = $totalEggs - $eggsNumber;
                }
                $entityManager->persist($eggsInputsDetailEggsDeliveries);
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
     * @Route("/new/{inputs}", name="eggs_inputs_details_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new($inputs,
                        Request $request,
                        DeliveryRepository $deliveryRepository,
                        InputsRepository $eggsInputsRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $entityManager = $this->getDoctrine()->getManager();
        $eggsInputsDetail = new InputsDetails();
        $form = $this->createForm(InputsDetailsType::class, $eggsInputsDetail);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eggsInputsDetail->setEggInput($inputs);
            $herd = $form['herd']->getData();
            $totalEggs = $form['eggsNumber']->getData();
            $deliveries = $deliveryRepository->eggsOnWarehouse($herd);

            $eggsNumber = $this->getEggsNumberInDeliveries($deliveries);

            if ($eggsNumber >= $totalEggs) {
                $entityManager->persist($eggsInputsDetail);

                $this->addDelivery($deliveries, $totalEggs, $eggsInputsDetail);

                return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
            }

        }

        return $this->render('eggs_inputs_details/new.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
            'form' => $form->createView(),
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_details_show", methods={"GET"})
     */
    public
    function show(InputsDetails $eggsInputsDetail): Response
    {
        return $this->render('eggs_inputs_details/show.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_details_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public
    function edit(Request $request, InputsDetails $eggsInputsDetail): Response
    {
        $form = $this->createForm(InputsDetailsType::class, $eggsInputsDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_details_index');
        }

        return $this->render('eggs_inputs_details/edit.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_details_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public
    function delete(Request $request, InputsDetails $eggsInputsDetail): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInputsDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_details_index');
    }
}
