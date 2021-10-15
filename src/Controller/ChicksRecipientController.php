<?php

namespace App\Controller;

use App\Entity\ChicksRecipient;
use App\Entity\PlanDeliveryChick;
use App\Entity\PlanIndicators;
use App\Form\ChicksRecipientType;
use App\Repository\ChicksRecipientRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/chicks_recipient")
 * @IsGranted("ROLE_USER")
 */
class ChicksRecipientController extends AbstractController
{
    /**
     * @Route("/", name="chicks_recipient_index", methods={"GET"})
     */
    public function index(ChicksRecipientRepository $chicksRecipientRepository): Response
    {
        return $this->render('chicks_recipient/index.html.twig', [
            'chicks_recipients' => $chicksRecipientRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/customer/{id}", name="chick_recipient_customer_index", methods={"GET"})
     */
    public function customerIndex(ChicksRecipientRepository $chicksRecipientRepository, CustomerRepository $customerRepository, $id): Response
    {
        $customer = $customerRepository->find($id);
        $chicksRecipients = $chicksRecipientRepository->findBy(['customer' => $customer]);

        return $this->render('chicks_recipient/index.html.twig', [
            'chicks_recipients' => $chicksRecipients,
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/new", name="chicks_recipient_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $chicksRecipient = new ChicksRecipient();
        $form = $this->createForm(ChicksRecipientType::class, $chicksRecipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chicksRecipient);
            $entityManager->flush();

            return $this->redirectToRoute('chicks_recipient_index');
        }

        return $this->render('chicks_recipient/new.html.twig', [
            'chicks_recipient' => $chicksRecipient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chicks_recipient_show", methods={"GET"})
     */
    public function show(ChicksRecipient $chicksRecipient, ChicksRecipientRepository $repository): Response
    {
        $inputsDetail = $repository->inputsDelivery($chicksRecipient->getId());
        $planDeliveryChickRepository = $this->getDoctrine()->getRepository(PlanDeliveryChick::class);
        $planDelivery = $planDeliveryChickRepository->findBy(['chickFarm' => $chicksRecipient], ['inputDate' => 'ASC']);
        $planIndicatorsRepository = $this->getDoctrine()->getRepository(PlanIndicators::class);
        $planIndicators = $planIndicatorsRepository->findOneBy([]);
        //        dd($planDelivery);

        return $this->render('chicks_recipient/show.html.twig', [
            'chicks_recipient' => $chicksRecipient,
            'inputsDetail' => $inputsDetail,
            'plan_delivery_chicks' => $planDelivery,
            'plan_indicators' => $planIndicators
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chicks_recipient_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, ChicksRecipient $chicksRecipient): Response
    {
        $form = $this->createForm(ChicksRecipientType::class, $chicksRecipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('chicks_recipient_index');
        }

        return $this->render('chicks_recipient/edit.html.twig', [
            'chicks_recipient' => $chicksRecipient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chicks_recipient_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, ChicksRecipient $chicksRecipient): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chicksRecipient->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chicksRecipient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chicks_recipient_index');
    }
}
