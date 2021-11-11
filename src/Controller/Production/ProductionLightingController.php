<?php

namespace App\Controller\Production;

use App\Entity\Herds;
use App\Entity\Inputs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionLightingController extends AbstractController
{
    /**
     * @Route("/lightings", name="production_lightings_index")
     */
    public function lightningsSite(): Response
    {
        $inputs = $this->inputsList();

        return $this->render('eggs_inputs_lighting/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/lightings/herd/{id}", name="production_lighting_herd")
     */
    public function lightingHerd(Inputs $inputs): Response
    {
        $herds = $this->herdInInput($inputs);

        return $this->render('eggs_inputs_lighting/production/herd.html.twig', [
            'herds' => $herds,
            'inputs' => $inputs,
        ]);
    }

    public function inputsList()
    {
        $inputsRepository = $this->getDoctrine()->getRepository(Inputs::class);
        $date = new \DateTime();
        $date->modify('-22 days');
        return $inputsRepository->inputsInProduction($date);
    }

    public function herdInInput($input)
    {
        $herdRepository = $this->getDoctrine()->getRepository(Herds::class);
        return $herdRepository->herdInInput($input);
    }

}
