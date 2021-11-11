<?php

namespace App\Controller\Production;

use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */

class ProductionTransferController extends AbstractController
{
    /**
     * @Route("/transfers", name="production_transfers_index")
     */
    public function transfersSite(): Response
    {
        $inputs = $this->inputsList();

        return $this->render('eggs_inputs_transfers/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/transfers/farm/{id}", name="production_transfer_farm")
     */
    public function transferFarm(Inputs $input): Response
    {
        $farms = $this->farmInInput($input);

        return $this->render('eggs_inputs_transfers/production/farm.html.twig', [
            'farms' => $farms,
            'inputs' => $input,
        ]);
    }

    /**
     * @Route("/transfers/herd/{farm}/{inputs}", name="production_transfer_herd")
     */
    public function transferHerd(InputsFarm $farm, Inputs $inputs): Response
    {
        $herds = $this->herdInInput($inputs);

        return $this->render('eggs_inputs_transfers/production/herd.html.twig', [
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
