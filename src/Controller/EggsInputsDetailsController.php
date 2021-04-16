<?php

namespace App\Controller;

use App\Entity\EggsDelivery;
use App\Entity\EggsInputsDetails;
use App\Entity\EggsInputsDetailsEggsDelivery;
use App\Entity\Herds;
use App\Form\EggsInputsDetailsType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\HerdsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_inputs_details")
 * @IsGranted("ROLE_USER")
 */
class EggsInputsDetailsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_details_index", methods={"GET"})
     */
    public function index(EggsInputsDetailsRepository $eggsInputsDetailsRepository): Response
    {
        return $this->render('eggs_inputs_details/index.html.twig', [
            'eggs_inputs_details' => $eggsInputsDetailsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_inputs_details_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsInputsDetail = new EggsInputsDetails();
        $form = $this->createForm(EggsInputsDetailsType::class, $eggsInputsDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $breeder = $form['breeder']->getData();
            $totalEggs = $form['eggsNumber']->getData();
            $herds = $entityManager->getRepository(Herds::class)->findBy(['breeder' => $breeder]);
            $entityManager->persist($eggsInputsDetail);
            foreach ($herds as $herd) {
                $deliveries = $entityManager->getRepository(EggsDelivery::class)->findBy(['herd' => $herd]);
                foreach ($deliveries as $delivery) {
                    $eggsNumber = $delivery->getEggsNumber();
                    if($eggsNumber > 0){
                        if($eggsNumber > $totalEggs){
                            $eggsInputsDetailEggsDeliveries = new EggsInputsDetailsEggsDelivery();
                            $eggsInputsDetailEggsDeliveries->setEggsDeliveries($delivery);
                            $eggsInputsDetailEggsDeliveries->setEggsInputDetails($eggsInputsDetail);
                            $eggsInputsDetailEggsDeliveries->setEggsNumber($totalEggs);
                        } else {
                            $eggsInputsDetailEggsDeliveries = new EggsInputsDetailsEggsDelivery();
                            $eggsInputsDetailEggsDeliveries->setEggsDeliveries($delivery);
                            $eggsInputsDetailEggsDeliveries->setEggsInputDetails($eggsInputsDetail);
                            $eggsInputsDetailEggsDeliveries->setEggsNumber($eggsNumber);
                            $totalEggs = $totalEggs - $eggsNumber;
                        }
                        $entityManager->persist($eggsInputsDetailEggsDeliveries);
                    }
                }
            }
//            dd($eggsInputsDetail);
            

            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_details_index');
        }

        return $this->render('eggs_inputs_details/new.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_details_show", methods={"GET"})
     */
    public function show(EggsInputsDetails $eggsInputsDetail): Response
    {
        return $this->render('eggs_inputs_details/show.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_details_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsInputsDetails $eggsInputsDetail): Response
    {
        $form = $this->createForm(EggsInputsDetailsType::class, $eggsInputsDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_details_index');
        }

        return $this->render('eggs_inputs_details/edit.html.twig', [
            'eggs_inputs_detail' => $eggsInputsDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_details_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggsInputsDetails $eggsInputsDetail): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eggsInputsDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInputsDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_details_index');
    }
}
