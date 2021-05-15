<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Entity\Lighting;
use App\Entity\Supplier;
use App\Form\LightingType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsLightingRepository;
use App\Repository\EggsInputsRepository;
use App\Repository\EggSupplierRepository;
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
    public function index(EggsInputsLightingRepository $eggsInputsLightingRepository): Response
    {
        return $this->render('eggs_inputs_lighting/index.html.twig', [
            'eggs_inputs_lightings' => $eggsInputsLightingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{inputs}/{breeder}", name="eggs_inputs_lighting_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new($inputs,
                        $breeder,
                        Request $request,
                        EggsInputsDetailsRepository $detailsRepository,
                        EggsInputsRepository $eggsInputsRepository,
                        EggSupplierRepository $eggSupplierRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $breeder = $eggSupplierRepository->find($breeder);
        $form = $this->createForm(LightingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalEggs = 0;
            $wasteEggs = $form['wasteEggs']->getData();
            $lightingDate = $form['lightingDate']->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $inputsDetails = $detailsRepository->inputBreederDetails($inputs, $breeder);

            /** @var  $inputDetail
             * Get sum eggs for breeder in eggs input
             */
            foreach ($inputsDetails as $inputDetail) {
                $totalEggs = $totalEggs + $inputDetail[1];
            }

            $totalWaste = 0;
            $length = count($inputsDetails) - 1;
            /** @var  $inputDetail
             * Added eggs lighting to eggs input details
             */
            foreach ($inputsDetails as $key => $inputDetail) {
                $eggsInputsLighting = new Lighting();
                $eggsInputsLighting->setEggsInputsDetail($inputDetail[0]);
                if ($key < $length) {
                    $setWaste = round($inputDetail[1] / $totalEggs * $wasteEggs, 0);
                    $eggsInputsLighting->setWasteEggs($setWaste);
                    $totalWaste = $totalWaste + $setWaste;
                } else {
                    $setWaste = $wasteEggs - $totalWaste;
                    $eggsInputsLighting->setWasteEggs($setWaste);
                }
                $eggsInputsLighting->setLightingDate($lightingDate);
                $entityManager->persist($eggsInputsLighting);
            }

            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_inputs_lighting/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \App\Repository\EggsInputsRepository $inputsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/lighting", name="no_lighting_index", methods={"GET"})
     */
    public function showNoLighting(EggsInputsRepository $inputsRepository): Response
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
