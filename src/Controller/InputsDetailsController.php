<?php

namespace App\Controller;

use App\Entity\EggsDelivery;
use App\Entity\EggsInputs;
use App\Entity\EggsInputsDetails;
use App\Entity\EggsInputsDetailsEggsDelivery;
use App\Entity\Herds;
use App\Form\EggsInputsDetailsType;
use App\Repository\EggsDeliveryRepository;
use App\Repository\EggsInputsDetailsEggsDeliveryRepository;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsRepository;
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
    public function index(EggsInputsDetailsRepository $eggsInputsDetailsRepository, EggsInputsDetailsEggsDeliveryRepository $repository): Response
    {
        $eggsInputsDetails = $eggsInputsDetailsRepository->deliveries();
        return $this->render('eggs_inputs_details/index.html.twig', [
            'eggs_inputs_details' => $eggsInputsDetails,
        ]);
    }

    /**
     * @Route("/new/{inputs}", name="eggs_inputs_details_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new($inputs,
                        Request $request,
                        EggsDeliveryRepository $deliveryRepository,
                        EggsInputsRepository $eggsInputsRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $entityManager = $this->getDoctrine()->getManager();
        $eggsInputsDetail = new EggsInputsDetails();
        $form = $this->createForm(EggsInputsDetailsType::class, $eggsInputsDetail);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eggsInputsDetail->setEggInput($inputs);
            $herd = $form['herd']->getData();
            $totalEggs = $form['eggsNumber']->getData();
            $deliveries = $deliveryRepository->eggsOnWarehouse($herd);
            $eggsNumber = 0;
            foreach ($deliveries as $delivery) {
                $eggsNumber = $eggsNumber + $delivery->getEggsNumber();
            }
            if ($eggsNumber > $totalEggs) {
                $entityManager->persist($eggsInputsDetail);
                foreach ($deliveries as $delivery) {
                    $eggsNumber = $delivery->getEggsOnWarehouse();
                    if ($totalEggs > 0 && $eggsNumber > 0) {
                        $eggsInputsDetailEggsDeliveries = new EggsInputsDetailsEggsDelivery();
                        $eggsInputsDetailEggsDeliveries->setEggsDeliveries($delivery);
                        $eggsInputsDetailEggsDeliveries->setEggsInputDetails($eggsInputsDetail);
                        if ($eggsNumber > $totalEggs) {
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

                return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
            }

        }

        return $this->render('eggs_inputs_details/new.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_details_show", methods={"GET"})
     */
    public
    function show(EggsInputsDetails $eggsInputsDetail): Response
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
    function edit(Request $request, EggsInputsDetails $eggsInputsDetail): Response
    {
        $form = $this->createForm(EggsInputsDetailsType::class, $eggsInputsDetail);
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
    function delete(Request $request, EggsInputsDetails $eggsInputsDetail): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInputsDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_details_index');
    }
}
