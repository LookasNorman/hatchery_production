<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerPhone;
use App\Form\CustomerPhoneType;
use App\Repository\CustomerPhoneRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer_phone")
 * @IsGranted("ROLE_USER")
 */
class CustomerPhoneController extends AbstractController
{
    /**
     * @Route("/", name="customer_phone_index", methods={"GET"})
     */
    public function index(CustomerPhoneRepository $customerPhoneRepository): Response
    {
        return $this->render('customer_phone/index.html.twig', [
            'customer_phones' => $customerPhoneRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{customer}", name="customer_phone_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_TRANSPORT') or is_granted('ROLE_MANAGER')")
     */
    public function new(Customer $customer = null, Request $request): Response
    {
        $customerPhone = new CustomerPhone();
        if($customer){
            $customerPhone->setCustomer($customer);
        }
        $form = $this->createForm(CustomerPhoneType::class, $customerPhone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerPhone);
            $entityManager->flush();

            return $this->redirectToRoute('customer_show', ['id' => $customerPhone->getCustomer()->getId()]);
        }

        return $this->render('customer_phone/new.html.twig', [
            'customer_phone' => $customerPhone,
            'form' => $form->createView(),
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/{id}", name="customer_phone_show", methods={"GET"})
     */
    public function show(CustomerPhone $customerPhone): Response
    {
        return $this->render('customer_phone/show.html.twig', [
            'customer_phone' => $customerPhone,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_phone_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_TRANSPORT') or is_granted('ROLE_MANAGER')")
     */
    public function edit(Request $request, CustomerPhone $customerPhone): Response
    {
        $form = $this->createForm(CustomerPhoneType::class, $customerPhone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_phone_index');
        }

        return $this->render('customer_phone/edit.html.twig', [
            'customer_phone' => $customerPhone,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_phone_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, CustomerPhone $customerPhone): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customerPhone->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customerPhone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_phone_index');
    }
}
