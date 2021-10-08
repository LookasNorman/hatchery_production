<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Entity\Lighting;
use App\Form\LightingType;
use App\Repository\HerdsRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\LightingRepository;
use App\Repository\InputsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/lighting")
 * @IsGranted("ROLE_USER")
 */
class LightingController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_lighting_index", methods={"GET"})
     */
    public function index(LightingRepository $eggsInputsLightingRepository): Response
    {
        return $this->render('eggs_inputs_lighting/index.html.twig', [
            'eggs_inputs_lightings' => $eggsInputsLightingRepository->findAll(),
        ]);
    }

    public function totalEggs($inputsFarmDelivery)
    {
        $totalEggs = 0;
        foreach ($inputsFarmDelivery as $inputFarmDelivery) {
            $totalEggs = $totalEggs + $inputFarmDelivery->getEggsNumber();
        }
        return $totalEggs;
    }

    public function createLighting($form, $inputsFarmDelivery )
    {
        $entityManager = $this->getDoctrine()->getManager();

        $wasteEggs = $form['wasteEggs']->getData();
        $lightingDate = $form['lightingDate']->getData();
        $eggsNumber = $form['eggsNumber']->getData();
        $totalEggs = $this->totalEggs($inputsFarmDelivery);

        $totalWaste = 0;
        $totalEggsNumber = 0;
        $length = count($inputsFarmDelivery) - 1;

        foreach ($inputsFarmDelivery as $key => $inputFarmDelivery) {
            $eggsInputLighting = new Lighting();
            $eggsInputLighting->setLightingDate($lightingDate);
            if ($totalEggs === $eggsNumber) {
                $setEggs = $inputFarmDelivery->getEggsNumber();
                $eggsInputLighting->setLightingEggs($setEggs);
            } else {
                if ($key < $length) {
                    $setEggs = round($eggsNumber / $totalEggs * $inputFarmDelivery->getEggsNumber());
                    $eggsInputLighting->setLightingEggs($setEggs);
                    $totalEggsNumber = $totalEggsNumber + $setEggs;
                } else {
                    $setEggs = $eggsNumber - $totalEggsNumber;
                    $eggsInputLighting->setLightingEggs($setEggs);
                }
            }
            $eggsInputLighting->addInputsFarmDelivery($inputFarmDelivery);

            if ($key < $length) {
                $setWaste = round($inputFarmDelivery->getEggsNumber() / $totalEggs * $wasteEggs, 0);
                $eggsInputLighting->setWasteEggs($setWaste);
                $totalWaste = $totalWaste + $setWaste;
            } else {
                $setWaste = $wasteEggs - $totalWaste;
                $eggsInputLighting->setWasteEggs($setWaste);
            }

            $fertility = round($setWaste / $setEggs + $inputFarmDelivery->getDelivery()->getHerd()->getLighting() / 100, 3);

            $wasteLighting = $inputFarmDelivery->getEggsNumber() * $fertility;
            $eggsInputLighting->setWasteLighting($wasteLighting);

            $entityManager->persist($eggsInputLighting);
        }
        $entityManager->flush();

    }

    /**
     * @Route("/new/{inputs}/{herd}", name="eggs_inputs_lighting_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(
        $inputs,
        $herd,
        Request $request,
        InputsRepository $eggsInputsRepository,
        HerdsRepository $herdsRepository,
        InputsFarmDeliveryRepository $inputsFarmDeliveryRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $herd = $herdsRepository->find($herd);
        $form = $this->createForm(LightingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputsFarmDelivery = $inputsFarmDeliveryRepository->findByExampleField($inputs, $herd);

            $this->createLighting($form, $inputsFarmDelivery);

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_inputs_lighting/new.html.twig', [
            'form' => $form->createView(),
            'input' => $inputs,
            'herd' => $herd
        ]);
    }

    /**
     * @param \App\Repository\InputsRepository $inputsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/lighting", name="no_lighting_index", methods={"GET"})
     */
    public function showNoLighting(InputsRepository $inputsRepository): Response
    {
        $lighting = $inputsRepository->inputsNoLighting();
        return $this->render('eggs_inputs/no_lighting.html.twig', [
            'eggs_inputs' => $lighting,
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_lighting_show", methods={"GET"})
     */
    public function show(Lighting $eggsInputsLighting): Response
    {
        return $this->render('eggs_inputs_lighting/show.html.twig', [
            'eggs_inputs_lighting' => $eggsInputsLighting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_lighting_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Lighting $eggsInputsLighting): Response
    {
        $form = $this->createForm(LightingType::class, $eggsInputsLighting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_lighting_index');
        }

        return $this->render('eggs_inputs_lighting/edit.html.twig', [
            'eggs_inputs_lighting' => $eggsInputsLighting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_lighting_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Lighting $eggsInputsLighting): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInputsLighting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsLighting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_lighting_index');
    }
}
