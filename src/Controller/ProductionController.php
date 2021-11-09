<?php

namespace App\Controller;

use App\Entity\ContactInfo;
use App\Entity\Delivery;
use App\Entity\Hatchers;
use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\Supplier;
use App\Form\DeliveryProductionType;
use App\Form\InputDeliveryProductionType;
use App\Repository\InputsRepository;
use App\Repository\SupplierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionController extends AbstractController
{
    public function inputsList()
    {
        $inputsRepository = $this->getDoctrine()->getRepository(Inputs::class);
        $date = new \DateTime();
        $date->modify('-22 days');
        $inputs = $inputsRepository->inputsInProduction($date);

        return $inputs;
    }

    /**
     * @Route("/", name="production_index")
     */
    public function productionSite()
    {
        return $this->render('main_page/production.html.twig');
    }

    /**
     * @Route("/inputs", name="production_inputs_index")
     */
    public function inputsSite()
    {
        $inputs = $this->inputsList();

        return $this->render('input_delivery/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/inputs/breeder/{id}", name="production_inputs_breeder", methods={"GET"})
     */
    public function inputsBreeder(Inputs $inputs)
    {
        $breeders = $this->getDoctrine()->getRepository(Supplier::class)->findBy([], ['name' => 'asc']);

        return $this->render('input_delivery/production/breeder.html.twig', [
            'input' => $inputs,
            'breeders' => $breeders
        ]);
    }

    /**
     * @Route("/inputs/herd/{inputs}/{supplier}", name="production_inputs_herd", methods={"GET"})
     */
    public function inputsHerd(Inputs $inputs, Supplier $supplier)
    {
        $herds = $this->getDoctrine()->getRepository(Herds::class)->findBy(['breeder' => $supplier, 'active' => true]);

        return $this->render('input_delivery/production/herd.html.twig', [
            'input' => $inputs,
            'breeder' => $supplier,
            'herds' => $herds
        ]);
    }

    public function herdDeliveryOnStock(Herds $herds)
    {
        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        $deliveries = $deliveryRepository->herdDeliveryOnStock($herds);
        return $deliveries;
    }

    public function checkStockEggs(Delivery $delivery)
    {
        $deliveryEggs = $delivery->getEggsNumber();
        $inputEggs = 0;
        foreach ($delivery->getInputDeliveries() as $inputDelivery) {
            $inputEggs = $inputEggs + $inputDelivery->getEggsNumber();
        }
        $sellingEggs = 0;
        foreach ($delivery->getSellingEggs() as $sellingEgg) {
            $sellingEggs = $sellingEggs + $sellingEgg->getEggsNumber();
        }
        return $deliveryEggs - $inputEggs - $sellingEggs;
    }

    /**
     * @Route("/inputs/new/{herd}/{input}", name="production_inputs_new", methods={"GET", "POST"})
     */
    public function inputNew(Herds $herd, Inputs $input, Request $request)
    {
        $form = $this->createForm(InputDeliveryProductionType::class);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $totalEggs = $form['eggs']->getData();
            $deliveries = $this->herdDeliveryOnStock($herd);
            foreach ($deliveries as $delivery) {
                $eggsNumber = $this->checkStockEggs($delivery);
                if ($totalEggs > 0) {
                    $inputDelivery = new InputDelivery();
                    if ($eggsNumber > 0) {
                        $inputDelivery->setDelivery($delivery);
                        $inputDelivery->setInput($input);
                        if ($eggsNumber >= $totalEggs) {
                            $inputDelivery->setEggsNumber($totalEggs);
                            $totalEggs = 0;
                        } else {
                            $inputDelivery->setEggsNumber($eggsNumber);
                            $totalEggs = $totalEggs - $eggsNumber;
                        }
                        $entityManager->persist($inputDelivery);
                    }
                }
            }
            $entityManager->flush();

            if ($form->get('saveBreeder')->isClicked()) {
                return $this->redirectToRoute('production_inputs_breeder', ['id' => $input->getId()]);
            }

            if ($form->get('saveHerd')->isClicked()) {
                return $this->redirectToRoute('production_inputs_herd', [
                    'inputs' => $input->getId(),
                    'supplier' => $herd->getBreeder()->getId()
                ]);
            }
            return $this->redirectToRoute('production_index');
        }

        return $this->render('input_delivery/production/new.html.twig', [
            'form' => $form->createView(),
            'herd' => $herd,
            'input' => $input
        ]);
    }

    /**
     * @Route("/transfers", name="production_transfers_index")
     */
    public function transfersSite()
    {
        $inputs = $this->inputsList();

        return $this->render('eggs_inputs_transfers/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/transfers/farm/{id}", name="production_transfer_farm")
     */
    public function transferFarm(Inputs $input)
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
    public function transferHerd(InputsFarm $farm, Inputs $inputs)
    {
        $herds = $this->herdInInputFarm($farm);

        return $this->render('eggs_inputs_transfers/production/herd.html.twig', [
            'herds' => $herds,
            'inputs' => $inputs,
            'farm' => $farm
        ]);
    }

    /**
     * @Route("/chick_temperature", name="production_chick_temperature_index")
     */
    public function chickTemperatureSite()
    {
        $inputs = $this->inputsList();

        return $this->render('chick_temperature/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/chick_temperature/hatcher/{input}", name="production_chick_temperature_hatcher")
     */
    public function chickTemperatureHatcher(Inputs $input)
    {
        $hatchers = $this->getDoctrine()->getRepository(Hatchers::class)->findAll();

        return $this->render('chick_temperature/production/hatcher.html.twig', [
            'hatchers' => $hatchers,
            'inputs' => $input,
        ]);
    }


    /**
     * @Route("/selections", name="production_selections_index")
     */
    public function selectionsSite()
    {
        $inputs = $this->inputsList();

        return $this->render('eggs_selections/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    /**
     * @Route("/selections/farm/{id}", name="production_selections_farm")
     */
    public function selectionsFarm(Inputs $input)
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
    public function selectionsHerd(InputsFarm $farm, Inputs $inputs)
    {
        $herds = $this->herdInInputFarm($farm);

        return $this->render('eggs_selections/production/herd.html.twig', [
            'herds' => $herds,
            'inputs' => $inputs,
            'farm' => $farm
        ]);
    }

    /**
     * @Route("/lightings", name="production_lightings_index")
     */
    public function lightingsSite()
    {
        $inputs = $this->inputsList();

        return $this->render('eggs_inputs_lighting/production/index.html.twig', [
            'inputs' => $inputs
        ]);
    }

    public function herdInInputFarm($farm)
    {
        $herdRepository = $this->getDoctrine()->getRepository(Herds::class);
        $herds = $herdRepository->herdInInputFarm($farm);

        return $herds;
    }

    public function herdInInput($input)
    {
        $herdRepository = $this->getDoctrine()->getRepository(Herds::class);
        $herds = $herdRepository->herdInInput($input);

        return $herds;
    }

    public function farmInInput($input)
    {
        $herdRepository = $this->getDoctrine()->getRepository(InputsFarm::class);
        $farm = $herdRepository->findBy(['eggInput' => $input]);

        return $farm;
    }

    /**
     * @Route("/lightings/herd/{id}", name="production_lighting_herd")
     */
    public function lightingHerd(Inputs $inputs)
    {
        $herds = $this->herdInInput($inputs);

        return $this->render('eggs_inputs_lighting/production/herd.html.twig', [
            'herds' => $herds,
            'inputs' => $inputs,
        ]);
    }

    /**
     * @Route("/delivery/supplier", name="production_delivery_supplier")
     */
    public function deliverySupplier(SupplierRepository $supplierRepository)
    {
        $suppliers = $supplierRepository->findBy([], ['name' => 'ASC']);

        return $this->render('eggs_delivery/production/supplier.html.twig', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * @Route("/delivery/herds/{id}", name="production_delivery_herd")
     */
    public function deliveryHerd(Supplier $supplier)
    {
        $herdsRepository = $this->getDoctrine()->getRepository(Herds::class);
        $herds = $herdsRepository->findBy(['breeder' => $supplier, 'active' => true], ['name' => 'ASC']);

        return $this->render('eggs_delivery/production/herd.html.twig', [
            'herds' => $herds
        ]);
    }


    public function sendEmail($eggsDelivery)
    {
        $contactInfoRepository = $this->getDoctrine()->getRepository(ContactInfo::class);
        $hatchery = $contactInfoRepository->findOneBy(['department' => 'Wylęgarnia']);
        $sales = $contactInfoRepository->findOneBy(['department' => 'Handel']);
        $accounting = $contactInfoRepository->findOneBy(['department' => 'Księgowość']);
        $emailAddress = $eggsDelivery->getHerd()->getBreeder()->getEmail();
        $date = $eggsDelivery->getDeliveryDate();

        $email = (new TemplatedEmail())
            ->to('rgolec@zwdmalec.pl')
            ->addBcc('lkonieczny@zwdmalec.pl')
            ->subject('Przyjęcie jaj w dniu ' . $date->format('Y-m-d'))
            ->htmlTemplate('emails/deliveryEgg.html.twig')
            ->context([
                'eggs_delivery' => $eggsDelivery,
                'hatchery' => $hatchery,
                'sales' => $sales,
                'accounting' => $accounting
            ]);
        if ($emailAddress) {
            $email->addTo($emailAddress);
        }
        return $email;
    }

    /**
     * @Route("/new/{id}", name="production_delivery_new", methods={"GET","POST"})
     */
    public function deliveryNew(Herds $herd, Request $request, MailerInterface $mailer): Response
    {
        $deliveryRepository = $this->getDoctrine()->getRepository(Delivery::class);
        $firstLaying = new \DateTime($deliveryRepository->lastHerdDelivery($herd));
        $eggsDelivery = new Delivery();
        $today = new \DateTime();
        $eggsDelivery->setDeliveryDate($today);
        $eggsDelivery->setLastLayingDate($today);
        $eggsDelivery->setFirstLayingDate($firstLaying);
        $eggsDelivery->setHerd($herd);
        $form = $this->createForm(DeliveryProductionType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $eggsDelivery->setEggsOnWarehouse($eggsDelivery->getEggsNumber());
            $entityManager->persist($eggsDelivery);
            $entityManager->flush();

            $email = $this->sendEmail($eggsDelivery);
            if ($email) {
                $mailer->send($email);
            }

            return $this->redirectToRoute('production_index');
        }

        return $this->render('eggs_delivery/production/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }
}
