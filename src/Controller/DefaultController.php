<?php

namespace App\Controller;

use App\Entity\InputsFarmDelivery;
use App\Repository\ChicksRecipientRepository;
use App\Repository\DeliveryRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\InputsFarmRepository;
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
     * @IsGranted("ROLE_USER")
     */
    public function index(
        SupplierRepository $supplierRepository,
        ChicksRecipientRepository $recipientRepository,
        InputsRepository $inputsRepository,
        DeliveryRepository $deliveryRepository,
        InputsFarmDeliveryRepository $inputsFarmDeliveryRepository,
        InputsFarmRepository $inputsFarmRepository
    ): Response
    {
        $suppliers = [];
        $eggsSuppliers = $supplierRepository->findAll();
        $suppliers['suppliersNumber'] = count($eggsSuppliers);
        $eggsInWarehouse = $deliveryRepository->eggsDelivered() - $inputsFarmDeliveryRepository->eggsProduction();

        $suppliers['eggsInWarehouse'] = $eggsInWarehouse;

        $recipients = [];
        $chicksRecipients = $recipientRepository->findAll();
        $recipients['recipientsNumber'] = count($chicksRecipients);

        $inputs = [];
        $eggsInputs = $inputsRepository->inputsNoSelection();
        $inputs['inputsNumber'] = count($eggsInputs);
        
        $lightings = $inputsRepository->lightingInputs();
        $transfers = $inputsRepository->transferInputs();
        $selectionsResult = $inputsRepository->inputsNoSelection();

        $selections = [];
        foreach ($selectionsResult as $selection){
            $chicks = $inputsFarmRepository->chickInInput($selection);
            array_push($selections, ['chicks' => $chicks, $selection]);
        }

        return $this->render('main_page/index.html.twig', [
            'suppliers' => $suppliers,
            'recipients' => $recipients,
            'inputs' => $inputs,
            'lightings' => $lightings,
            'transfers' => $transfers,
            'selections' => $selections
        ]);
    }

    /**
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
