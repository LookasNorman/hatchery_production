<?php

namespace App\Controller;

use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use App\Entity\InputsFarmDelivery;
use App\Entity\Lighting;
use App\Form\LightingCorrectType;
use App\Form\LightingType;
use App\Repository\HerdsRepository;
use App\Repository\InputDeliveryRepository;
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
            'eggs_inputs_lightings' => $eggsInputsLightingRepository->findBy([], ['lightingDate' =>'desc']),
        ]);
    }

    /**
     * @Route("/new/{inputs}/{herd}", name="eggs_inputs_lighting_new", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function new(
        $inputs,
        $herd,
        Request $request,
        InputsRepository $eggsInputsRepository,
        HerdsRepository $herdsRepository,
        InputDeliveryRepository $inputDeliveryRepository
    ): Response
    {
        $inputs = $eggsInputsRepository->find($inputs);
        $herd = $herdsRepository->find($herd);
        $form = $this->createForm(LightingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputDelivery = $inputDeliveryRepository->herdDeliveryInInputForLighting($herd, $inputs);

            $this->createLighting($form, $inputDelivery);

            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            if (in_array('ROLE_PRODUCTION', $user->getRoles(), true)) {
                return $this->redirectToRoute('production_lighting_herd', ['id' => $inputs->getId()]);
            }
            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('eggs_inputs_lighting/new.html.twig', [
            'form' => $form->createView(),
            'input' => $inputs,
            'herd' => $herd
        ]);
    }

    public function totalEggs($inputDeliveries)
    {
        $totalEggs = 0;
        foreach ($inputDeliveries as $inputDelivery) {
            $totalEggs = $totalEggs + $inputDelivery->getEggsNumber();
        }
        return $totalEggs;
    }

    public function createLighting($form, $inputDeliveries)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $wasteEggs = $form['wasteEggs']->getData();
        $lightingDate = $form['lightingDate']->getData();
        $eggsNumber = $form['eggsNumber']->getData();
        $totalEggs = $this->totalEggs($inputDeliveries);
        $fertility = round($wasteEggs / $eggsNumber, 4);
        $totalWaste = 0;
        $totalEggsNumber = 0;
        $length = count($inputDeliveries) - 1;
        foreach ($inputDeliveries as $key => $inputDelivery) {
            $eggsInputLighting = new Lighting();
            $eggsInputLighting->setLightingDate($lightingDate);
            if ($totalEggs === $eggsNumber) {
                $setEggs = $inputDelivery->getEggsNumber();
                $eggsInputLighting->setLightingEggs($setEggs);
                dd($inputDelivery);
            } else {
                if ($key < $length) {
                    $setEggs = round($eggsNumber / $totalEggs * $inputDelivery->getEggsNumber());
                    $eggsInputLighting->setLightingEggs($setEggs);
                    $totalEggsNumber = $totalEggsNumber + $setEggs;
                } else {
                    $setEggs = $eggsNumber - $totalEggsNumber;
                    $eggsInputLighting->setLightingEggs($setEggs);
                }
            }
            $inputDelivery->getDelivery()->addLightingEggs($setEggs);

            $eggsInputLighting->addInputDelivery($inputDelivery);
            $setWasteLighting = $inputDelivery->getEggsNumber() * $fertility;
            $eggsInputLighting->setWasteLighting($setWasteLighting);
            $inputDelivery->getDelivery()->addWasteLighting($setWasteLighting);

            if ($key < $length) {
                $setWaste = round($inputDelivery->getEggsNumber() / $totalEggs * $wasteEggs, 0);
                $eggsInputLighting->setWasteEggs($setWaste);
                $totalWaste = $totalWaste + $setWaste;
            } else {
                $setWaste = $wasteEggs - $totalWaste;
                $eggsInputLighting->setWasteEggs($setWaste);
            }
            $inputDelivery->getDelivery()->addWasteEggLighting($setWaste);
            $entityManager->persist($eggsInputLighting);
        }
        $entityManager->flush();

    }

    /**
     * @Route("/correct/{inputs}/{herd}", name="eggs_inputs_lighting_correct", methods={"GET","POST"})
     * @IsGranted("ROLE_PRODUCTION")
     */
    public function correct(
        Request $request,
                $inputs,
                $herd
    ): Response
    {
        $inputs = $this->getDoctrine()->getRepository(Inputs::class)->find($inputs);
        $herd = $this->getDoctrine()->getRepository(Herds::class)->find($herd);

        $inputsFarmDeliveries = $this->getDoctrine()->getRepository(InputsFarmDelivery::class)->inputFarmDeliveryForLighting($herd, $inputs);
        $lightinsEggs = 0;
        $wasteEggs = 0;
        foreach ($inputsFarmDeliveries as $inputsFarmDelivery) {
            $lightinsEggs = $lightinsEggs + $inputsFarmDelivery->getLighting()->getLightingEggs();
            $wasteEggs = $wasteEggs + $inputsFarmDelivery->getLighting()->getWasteEggs();
        }
        $fertilization = round((1 - $wasteEggs / $lightinsEggs) * 100, 1);
        $lightings = ['lightingEggs' => $lightinsEggs, 'wasteEggs' => $wasteEggs, 'fertilization' => $fertilization];

        $form = $this->createForm(LightingCorrectType::class);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $correctPercent = 1 - $form['correctPercent']->getData();

            foreach ($inputsFarmDeliveries as $inputsFarmDelivery) {

                $eggs = $inputsFarmDelivery->getEggsNumber();
                $eggsLighting = $inputsFarmDelivery->getLighting();
                $lightinsEggs = $eggs * $correctPercent;
                $eggsLighting->setWasteLighting($lightinsEggs);
                $em->persist($eggsLighting);
            }
            $em->flush();

            return $this->redirectToRoute('eggs_inputs_show', [
                'id' => $inputs->getId()
            ]);
        }

        return $this->render('eggs_inputs_lighting/correct.html.twig', [
            'form' => $form->createView(),
            'input' => $inputs,
            'herd' => $herd,
            'lightings' => $lightings
        ]);
    }


    /**
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
     * @Route("/{id}", name="eggs_inputs_lighting_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Lighting $lighting): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lighting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($lighting->getInputDeliveries() as $delivery) {
                $delivery->getDelivery()->oddWasteLighting($lighting->getWasteLighting());
                $delivery->getDelivery()->oddLightingEggs($lighting->getLightingEggs());
                $delivery->getDelivery()->oddWasteEggLighting($lighting->getWasteEggs());
                $entityManager->persist($delivery);
                $lighting->removeInputDelivery($delivery);
            }

            $entityManager->remove($lighting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_lighting_index');
    }
}
