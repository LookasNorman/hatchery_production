<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputsDetails;
use App\Form\HerdsType;
use App\Repository\DetailsRepository;
use App\Repository\SupplierRepository;
use App\Repository\HerdsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/herds")
 * @IsGranted("ROLE_USER")
 */
class HerdsController extends AbstractController
{
    /**
     * @Route("/", name="herds_index", methods={"GET"})
     */
    public function index(HerdsRepository $herdsRepository): Response
    {
        return $this->render('herds/index.html.twig', [
            'herds' => $herdsRepository->findBy([] , ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/breeder/{id}", name="herds_breeder_index", methods={"GET"})
     */
    public function breeder(HerdsRepository $herdsRepository, SupplierRepository $eggSupplierRepository, $id): Response
    {
        $breeder = $eggSupplierRepository->find($id);
        return $this->render('herds/index.html.twig', [
            'herds' => $herdsRepository->findBy(['breeder' => $breeder]),
            'breeder' => $breeder,
        ]);
    }

    /**
     * @Route("/new", name="herds_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $herd = new Herds();
        $form = $this->createForm(HerdsType::class, $herd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($herd);
            $entityManager->flush();

            return $this->redirectToRoute('herds_index');
        }

        return $this->render('herds/new.html.twig', [
            'herd' => $herd,
            'form' => $form->createView(),
        ]);
    }

    public function herdsDelivery(Herds $herd)
    {
        $detailsRepository = $this->getDoctrine()->getRepository(InputsDetails::class);
        $inputDetails = $detailsRepository->deliveriesHerd($herd);

        $inputsDetails = [];
        foreach ($inputDetails as $inputDetail) {
            $totalEggs = 0;
            $totalWasteLightings = 0;
            $totalWasteTransfers = 0;
            $totalChick = 0;
            $totalCullChick = 0;

            $detailDeliveries = $inputDetail->getEggsInputsDetailsEggsDeliveries();
            foreach ($detailDeliveries as $detailDelivery) {
                $totalEggs = $totalEggs + $detailDelivery->getEggsNumber();
            }

            $lightings = $inputDetail->getEggsInputsLightings();
            foreach ($lightings as $lighting) {
                $totalWasteLightings = $totalWasteLightings + $lighting->getWasteEggs();
            }

            $transfers = $inputDetail->getEggsInputsTransfers();
            foreach ($transfers as $transfer) {
                $totalWasteTransfers = $totalWasteTransfers + $transfer->getWasteEggs();
            }

            $selections = $inputDetail->getEggsSelections();
            foreach ($selections as $selection){
                $totalChick = $totalChick + $selection->getChickNumber();
                $totalCullChick = $totalCullChick + $selection->getCullChicken();
            }

            foreach ($detailDeliveries as $detailDelivery) {
                $delivery = $detailDelivery->getEggsDeliveries();
                $deliveryEggs = $detailDelivery->getEggsNumber();
                $wasteLighting = $deliveryEggs / $totalEggs * $totalWasteLightings;
                $wasteTransfer = $deliveryEggs / $totalEggs * $totalWasteTransfers;
                $chickNumber = $deliveryEggs / $totalEggs * $totalChick;
                $cullChick = $deliveryEggs / $totalEggs * $totalCullChick;
                if($chickNumber){
                    $unhatched = $deliveryEggs - $wasteLighting - $wasteTransfer - $chickNumber - $cullChick;
                } else {
                    $unhatched = null;
                }
                $inputsDetails [] = [
                    'inputId' => $inputDetail->getEggInput()->getId(),
                    'inputName' => $inputDetail->getEggInput()->getName(),
                    'deliveryId' => $delivery->getId(),
                    'deliveryDate' => $delivery->getDeliveryDate(),
                    'deliveryEggs' => $deliveryEggs,
                    'wasteLighting' => $wasteLighting,
                    'wasteTransfer' => $wasteTransfer,
                    'chickNumber' => $chickNumber,
                    'cullChick' => $cullChick,
                    'unhatched' => $unhatched,
                ];
            }
        }

        return [
            'inputDetails' => $inputDetails,
            'inputsDetails' => $inputsDetails,
        ];

    }

    /**
     * @Route("/{id}", name="herds_show", methods={"GET"})
     */
    public function show(Herds $herd): Response
    {
        $inputDetails = $this->herdsDelivery($herd);
        $deliveries = $this->herdDeliveries($herd);

        return $this->render('herds/show.html.twig', [
            'herd' => $herd,
            'deliveries' => $deliveries,
            'inputDetails' => $inputDetails['inputDetails'],
            'inputsDetails' => $inputDetails['inputsDetails'],
        ]);
    }

    public function herdDeliveries($herd): array
    {
        $deliveries = $this->getDoctrine()->getManager()->getRepository(Delivery::class)->findBy(['herd' => $herd]);

        return $deliveries;
    }

    /**
     * @Route("/{id}/edit", name="herds_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Herds $herd): Response
    {
        $form = $this->createForm(HerdsType::class, $herd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('herds_index');
        }

        return $this->render('herds/edit.html.twig', [
            'herd' => $herd,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="herds_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Herds $herd): Response
    {
        if ($this->isCsrfTokenValid('delete' . $herd->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($herd);
            $entityManager->flush();
        }

        return $this->redirectToRoute('herds_index');
    }
}
