<?php

namespace App\Controller;

use App\Entity\ChicksRecipient;
use App\Entity\Customer;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\InputsFarmDelivery;
use App\Repository\ChicksRecipientRepository;
use App\Repository\DeliveryRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\InputsFarmRepository;
use App\Repository\InputsRepository;
use App\Repository\SupplierRepository;
use App\Repository\HatchersRepository;
use App\Repository\SettersRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Snappy\Pdf;

/**
 * @IsGranted("ROLE_USER")
 */
class DefaultController extends AbstractController
{

    /**
     * @Route("/pdf")
     * @IsGranted("ROLE_MANAGER")
     */
    public function indexPdf(Pdf $pdf, ChicksRecipientRepository $chicksRecipientRepository)
    {
        $html = $this->renderView('chicks_recipient/index.html.twig', array(
            'chicks_recipients' => $chicksRecipientRepository->findBy([], ['name' => 'ASC'])
        ));

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }

    public function farmCount()
    {
        $chickRecipientRepository = $this->getDoctrine()->getRepository(ChicksRecipient::class);
        $farm = [];
        $chicksRecipients = $chickRecipientRepository->findAll();
        $farm['farmNumber'] = count($chicksRecipients);
        return $farm;
    }

    public function customerCount()
    {
        $customerRepository = $this->getDoctrine()->getRepository(Customer::class);
        $customer = [];
        $customers = $customerRepository->findAll();
        $customer['recipientsNumber'] = count($customers);
        return $customer;
    }

    public function chicksInputsProduction($inputs)
    {
        $inputsFarmRepository = $this->getDoctrine()->getRepository(InputsFarm::class);
        $chicks = 0;
        foreach ($inputs as $input) {
            $chicks = $chicks + $inputsFarmRepository->chickInInput($input);
        }
        return $chicks;
    }

    public function eggsInProduction()
    {
        $inputsFarmDeliveryRepository = $this->getDoctrine()->getRepository(InputsFarmDelivery::class);
        $eggs = $inputsFarmDeliveryRepository->eggsInSetters() + $inputsFarmDeliveryRepository->eggsInHatchers();

        return $eggs;
    }

    public function inputsCount()
    {
        $inputsRepository = $this->getDoctrine()->getRepository(Inputs::class);
        $eggsInputs = $inputsRepository->inputsNoSelection();
        $chicks = $this->chicksInputsProduction($eggsInputs);
        $eggs = $this->eggsInProduction();
        $inputs = ['inputsNumber' => count($eggsInputs), 'chicks' => $chicks, 'eggsProduction' => $eggs];

        return $inputs;
    }

    /**
     * @Route("/", name="main_page")
     */
    public function index(
        SupplierRepository           $supplierRepository,
        InputsRepository             $inputsRepository,
        DeliveryRepository           $deliveryRepository,
        InputsFarmDeliveryRepository $inputsFarmDeliveryRepository,
        InputsFarmRepository         $inputsFarmRepository
    ): Response
    {
        $suppliers = [];
        $eggsSuppliers = $supplierRepository->findAll();
        $suppliers['suppliersNumber'] = count($eggsSuppliers);
        $eggsInWarehouse = $deliveryRepository->eggsDelivered() - $inputsFarmDeliveryRepository->eggsProduction();

        $suppliers['eggsInWarehouse'] = $eggsInWarehouse;
        $farm = $this->farmCount();
        $recipients = $this->customerCount();
        $inputs = $this->inputsCount();

        $lightings = $inputsRepository->lightingInputs();
        $transfers = $inputsRepository->transferInputs();
        $selectionsResult = $inputsRepository->inputsNoSelection();

        $selections = [];
        foreach ($selectionsResult as $selection) {
            $chicks = $inputsFarmRepository->chickInInput($selection);
            array_push($selections, ['chicks' => $chicks, $selection]);
        }

        return $this->render('main_page/index.html.twig', [
            'suppliers' => $suppliers,
            'recipients' => $recipients,
            'farms' => $farm,
            'inputs' => $inputs,
            'lightings' => $lightings,
            'transfers' => $transfers,
            'selections' => $selections
        ]);
    }

    /**
     * @Route("/incubators", name="incubators_index")
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
