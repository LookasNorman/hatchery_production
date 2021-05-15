<?php

namespace App\Controller;

use App\Repository\ChicksRecipientRepository;
use App\Repository\DeliveryRepository;
use App\Repository\DetailsRepository;
use App\Repository\InputsRepository;
use App\Repository\SupplierRepository;
use App\Repository\HatchersRepository;
use App\Repository\SettersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index(
        SupplierRepository $supplierRepository,
        ChicksRecipientRepository $recipientRepository,
        InputsRepository $inputsRepository,
        DeliveryRepository $deliveryRepository,
        DetailsRepository $detailsRepository
    ): Response
    {
        $suppliers = [];
        $eggsSuppliers = $supplierRepository->findAll();
        $suppliers['suppliersNumber'] = count($eggsSuppliers);
        $eggsInWarehouse = $deliveryRepository->eggsInWarehouse();
        $suppliers['eggsInWarehouse'] = $eggsInWarehouse[0]['eggsInWarehouse'];

        $recipients = [];
        $chicksRecipients = $recipientRepository->findAll();
        $recipients['recipientsNumber'] = count($chicksRecipients);

        $inputs = [];
        $eggsInputs = $inputsRepository->findAll();
        $inputs['inputsNumber'] = count($eggsInputs);
        $eggs = $detailsRepository->eggs();
        $chicks = $detailsRepository->chicks();
        $inputs['chicksNumber'] = $chicks['chickNumber'];
        $inputs['eggsNumber'] = $eggs['eggsNumber'];

        $lighting = $inputsRepository->inputsLighting();
        $transfer = $inputsRepository->inputsTransfers();
        $picking = $inputsRepository->inputsPickings();

        return $this->render('main_page/index.html.twig', [
            'suppliers' => $suppliers,
            'recipients' => $recipients,
            'inputs' => $inputs,
            'lighting' => $lighting,
            'transfer' => $transfer,
            'picking' => $picking,
        ]);
    }

    /**
     * @param \App\Repository\SettersRepository $settersRepository
     * @param \App\Repository\HatchersRepository $hatchersRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/incubators", name="incubators_index")
     * @IsGranted("ROLE_USER")
     */
    public function incubators(SettersRepository $settersRepository, HatchersRepository $hatchersRepository)
    {
        $setters = $settersRepository->findAll();
        $hatchers = $hatchersRepository->findAll();
        return $this->render('incubators/index.html.twig', [
            'setters' => $setters,
            'hatchers' => $hatchers,
        ]);
    }
}
