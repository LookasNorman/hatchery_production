<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\Supplier;
use App\Form\DeliveryPartIndexType;
use App\Form\DeliveryType;
use App\Repository\DeliveryRepository;
use App\Repository\DetailsDeliveryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/delivery")
 * @IsGranted("ROLE_USER")
 */
class DeliveryController extends AbstractController
{
    /**
     * @Route("/", name="eggs_delivery_index", methods={"GET"})
     */
    public function index(DeliveryRepository $eggsDeliveryRepository): Response
    {
        return $this->render('eggs_delivery/index.html.twig', [
            'eggs_deliveries' => $eggsDeliveryRepository->deliveryOnWarehouse(),
        ]);
    }

    /**
     * @Route("/all", name="eggs_delivery_all_index", methods={"GET"})
     */
    public function allDelivery(DeliveryRepository $eggsDeliveryRepository): Response
    {
        return $this->render('eggs_delivery/index.html.twig', [
            'eggs_deliveries' => $eggsDeliveryRepository->findBy([], ['deliveryDate' => 'asc']),
        ]);
    }

    public function addHerd($name, $supplier, $breed)
    {
        $em = $this->getDoctrine()->getManager();
        $herd = new Herds();
        $herd->setName($name);
        $herd->setBreeder($supplier);
        $herd->setBreed($breed);
        $hatchingDate = new \DateTime('2019-03-31');
        $herd->setHatchingDate($hatchingDate);
        $em->persist($herd);
        $em->flush();

        return $herd;
    }

    public function addSupplier($name)
    {
        $em = $this->getDoctrine()->getManager();
        $supplier = new Supplier();
        $supplier->setName($name);
        $em->persist($supplier);
        $em->flush();

        return $supplier;
    }

    /**
     * @Route("/import", name="delivery_import", methods={"GET"})
     * @throws \Exception
     */
    public function importDeliveryXls()
    {
        $em = $this->getDoctrine()->getManager();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        $spreadsheet = $reader->load("delivery.xls");
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            $deliveryDate = new \DateTime($worksheet->getCell('A' . $row->getRowIndex())->getFormattedValue());
            $name = $worksheet->getCell('B' . $row->getRowIndex())->getFormattedValue();
            $firstLayingDate = new \DateTime($worksheet->getCell('C' . $row->getRowIndex())->getFormattedValue());
            $lastLayingDate = new \DateTime($worksheet->getCell('D' . $row->getRowIndex())->getFormattedValue());
            $eggsNumber = $worksheet->getCell('E' . $row->getRowIndex())->getValue();

            $herd = $em->getRepository(Herds::class)->findOneBy(['name' => $name]);
            if (!$herd instanceof Herds) {
                $nameArr = explode(' ', $name);
                $supplier = $em->getRepository(Supplier::class)->findOneBy(['name' => $nameArr[0]]);
                if (!$supplier instanceof Supplier) {
                    $supplier = $this->addSupplier($nameArr[0]);
                }
                $breed = $em->getRepository(Breed::class)->find(3);
                $herd = $this->addHerd($name, $supplier, $breed);
            }
            $delivery = new Delivery();
            $delivery->setHerd($herd);
            $delivery->setDeliveryDate($deliveryDate);
            $delivery->setFirstLayingDate($firstLayingDate);
            $delivery->setLastLayingDate($lastLayingDate);
            $delivery->setEggsNumber($eggsNumber);
            $delivery->setEggsOnWarehouse($eggsNumber);
            $em->persist($delivery);
        }
        $em->flush();
        die();
    }

    /**
     * @Route("/new", name="eggs_delivery_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsDelivery = new Delivery();
        $form = $this->createForm(DeliveryType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $eggsDelivery->setEggsOnWarehouse($eggsDelivery->getEggsNumber());
            $entityManager->persist($eggsDelivery);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/new.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_delivery_show", methods={"GET"})
     */
    public function show(Delivery $eggsDelivery): Response
    {
        return $this->render('eggs_delivery/show.html.twig', [
            'eggs_delivery' => $eggsDelivery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_delivery_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Delivery $eggsDelivery): Response
    {
        $form = $this->createForm(DeliveryType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/edit.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/add", name="eggs_delivery_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addPartIndex(Request $request, Delivery $eggsDelivery)
    {
        $form = $this->createForm(DeliveryPartIndexType::class, $eggsDelivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_delivery_index');
        }

        return $this->render('eggs_delivery/addPartIndex.html.twig', [
            'eggs_delivery' => $eggsDelivery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_delivery_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Delivery $eggsDelivery): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsDelivery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsDelivery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_delivery_index');
    }
}
