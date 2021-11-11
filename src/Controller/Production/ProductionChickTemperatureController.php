<?php

namespace App\Controller\Production;

use App\Entity\Hatchers;
use App\Entity\Inputs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionChickTemperatureController extends AbstractController
{
    /**
     * @Route("/chick_temperature", name="production_chick_temperature_index")
     */
    public function chickTemperatureSite(): Response
    {
        $inputs = $this->inputsList();

        return $this->render('chick_temperature/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/chick_temperature/hatcher/{input}", name="production_chick_temperature_hatcher")
     */
    public function chickTemperatureHatcher(Inputs $input): Response
    {
        $hatcheries = $this->getDoctrine()->getRepository(Hatchers::class)->findAll();

        return $this->render('chick_temperature/production/hatcher.html.twig', [
            'hatcheries' => $hatcheries,
            'inputs' => $input,
        ]);
    }

    public function inputsList()
    {
        $inputsRepository = $this->getDoctrine()->getRepository(Inputs::class);
        $date = new \DateTime();
        $date->modify('-22 days');
        return $inputsRepository->inputsInProduction($date);
    }

}
