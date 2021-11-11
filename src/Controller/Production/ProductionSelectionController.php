<?php

namespace App\Controller\Production;

use App\Entity\Delivery;
use App\Entity\Hatchers;
use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\Supplier;
use App\Form\InputDeliveryProductionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionSelectionController extends AbstractController
{
    /**
     * @Route("/selections", name="production_selections_index")
     */
    public function selectionsSite(): Response
    {
        $inputs = $this->inputsList();

        return $this->render('eggs_selections/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/selections/farm/{id}", name="production_selections_farm")
     */
    public function selectionsFarm(Inputs $input): Response
    {
        $farms = $this->farmInInput($input);

        return $this->render('eggs_selections/production/farm.html.twig', [
            'farms' => $farms,
            'inputs' => $input,
        ]);
    }

    /**
     * @Route("/selections/herd/{farm}/{inputs}", name="production_selections_herd")
     */
    public function selectionsHerd(InputsFarm $farm, Inputs $inputs): Response
    {
        $herds = $this->herdInInput($inputs);

        return $this->render('eggs_selections/production/herd.html.twig', [
            'herds' => $herds,
            'inputs' => $inputs,
            'farm' => $farm
        ]);
    }

    public function inputsList()
    {
        $inputsRepository = $this->getDoctrine()->getRepository(Inputs::class);
        $date = new \DateTime();
        $date->modify('-22 days');
        return $inputsRepository->inputsInProduction($date);
    }

    public function farmInInput($input): array
    {
        $herdRepository = $this->getDoctrine()->getRepository(InputsFarm::class);
        return $herdRepository->findBy(['eggInput' => $input]);
    }

    public function herdInInput($input)
    {
        $herdRepository = $this->getDoctrine()->getRepository(Herds::class);
        return $herdRepository->herdInInput($input);
    }

}
