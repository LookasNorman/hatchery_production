<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputsDetails;
use App\Entity\InputsFarmDelivery;
use App\Form\HerdActiveType;
use App\Form\HerdsType;
use App\Repository\DeliveryRepository;
use App\Repository\DetailsRepository;
use App\Repository\InputDeliveryRepository;
use App\Repository\InputsFarmDeliveryRepository;
use App\Repository\InputsRepository;
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
            'herds' => $herdsRepository->findBy([] , ['active' => 'DESC', 'name' => 'ASC']),
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
     * @IsGranted("ROLE_MANAGER")
     */
    public function new(Request $request): Response
    {
        $herd = new Herds();
        $herd->setActive(true);
        $form = $this->createForm(HerdsType::class, $herd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $herd->setActive(true);
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

    /**
     * @Route("/{id}", name="herds_show", methods={"GET"})
     */
    public function show(
        Herds $herd,
        DeliveryRepository $deliveryRepository,
        InputDeliveryRepository $inputDeliveryRepository
    ): Response
    {
        $deliveries = $deliveryRepository->herdDeliveryOnStock($herd);
        $inputs = $inputDeliveryRepository->herdDelivery($herd);

//        $inputs = $deliveryRepository->herdDelivery($herd);

        return $this->render('herds/show.html.twig', [
            'herd' => $herd,
            'deliveries' => $deliveries,
            'inputs' => $inputs
        ]);
    }

    public function herdDeliveries($herd): array
    {
        $deliveries = $this->getDoctrine()->getManager()->getRepository(Delivery::class)->findBy(['herd' => $herd]);

        return $deliveries;
    }

    /**
     * @Route("/{id}/edit", name="herds_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
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
     * @Route("/{id}/active", name="herds_edit_active", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function active(Request $request, Herds $herd): Response
    {
        if ($this->isCsrfTokenValid('active_herd' . $herd->getId(), $request->request->get('_token_active'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $herd->setActive(!$herd->getActive());
            $entityManager->flush();
        }

        return $this->redirectToRoute('egg_supplier_show', [
            'id' => $herd->getBreeder()->getId()
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
