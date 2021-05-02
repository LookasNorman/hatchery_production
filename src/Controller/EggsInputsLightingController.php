<?php

namespace App\Controller;

use App\Entity\EggsInputs;
use App\Entity\EggsInputsLighting;
use App\Entity\EggSupplier;
use App\Form\EggsInputsLightingType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsLightingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_inputs_lighting")
 * @IsGranted("ROLE_USER")
 */
class EggsInputsLightingController extends AbstractController
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
     * @Route("/new", name="eggs_inputs_lighting_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EggsInputsDetailsRepository $detailsRepository): Response
    {
        $form = $this->createForm(EggsInputsLightingType::class);
        $form->handleRequest($request);

        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            $form['breeder']->getData() instanceof EggSupplier &&
            $form['eggsInputs']->getData() instanceof EggsInputs
        ) {
            $totalEggs = 0;
            $inputs = $form['eggsInputs']->getData();
            $breeder = $form['breeder']->getData();
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
            $length = count($inputsDetails);
            /** @var  $inputDetail
             * Added eggs lighting to eggs input details
             */
            foreach ($inputsDetails as $key => $inputDetail) {
                $eggsInputsLighting = new EggsInputsLighting();
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

            return $this->redirectToRoute('eggs_inputs_lighting_index');
        }

        return $this->render('eggs_inputs_lighting/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_lighting_show", methods={"GET"})
     */
    public function show(EggsInputsLighting $eggsInputsLighting): Response
    {
        return $this->render('eggs_inputs_lighting/show.html.twig', [
            'eggs_inputs_lighting' => $eggsInputsLighting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_lighting_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsInputsLighting $eggsInputsLighting): Response
    {
        $form = $this->createForm(EggsInputsLightingType::class, $eggsInputsLighting);
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
    public function delete(Request $request, EggsInputsLighting $eggsInputsLighting): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInputsLighting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsLighting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_lighting_index');
    }
}
