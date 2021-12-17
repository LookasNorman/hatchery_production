<?php

namespace App\Controller;

use App\Entity\ContactInfo;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Form\InputsFarmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inputs_farm")
 * @IsGranted("ROLE_USER")
 */
class InputsFarmController extends AbstractController
{

    /**
     * @Route("/new/{id}", name="inputs_farm_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function new(Request $request, Inputs $inputs = null, MailerInterface $mailer): Response
    {
        $inputsFarm = new InputsFarm();
        $inputsFarm->setEggInput($inputs);
        $form = $this->createForm(InputsFarmType::class, $inputsFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($inputsFarm);
            $email = $this->sendEmail($inputsFarm);
            if ($email) {
                $mailer->send($email);
            }
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('inputs_farm/new.html.twig', [
            'inputs_farm' => $inputsFarm,
            'form' => $form->createView(),
        ]);
    }

    public function sendEmail($inputsFarm)
    {
        $contactInfoRepository = $this->getDoctrine()->getRepository(ContactInfo::class);
        $hatchery = $contactInfoRepository->findOneBy(['department' => 'Wylęgarnia']);
        $sales = $contactInfoRepository->findOneBy(['department' => 'Handel']);
        $accounting = $contactInfoRepository->findOneBy(['department' => 'Księgowość']);
        $emailAddress = null;
//        $emailAddress = $inputsFarm->getChicksFarm()->getEmail();

        $email = (new TemplatedEmail())
            ->to('rgolec@zwdmalec.pl')
            ->addTo('kkrakowiak@zwdmalec.pl')
            ->addBcc('lkonieczny@zwdmalec.pl')
            ->subject('Wprowadzono nakład ' . $inputsFarm->geteggInput()->getInputDate()->format('Y-m-d'))
            ->htmlTemplate('emails/inputFarm.html.twig')
            ->context([
                'inputFarm' => $inputsFarm,
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
     * @Route("/new_plan/{id}", name="inputs_farm_plan_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function newPlan(Request $request, Inputs $inputs = null): Response
    {
        $inputsFarm = new InputsFarm();
        $inputsFarm->setEggInput($inputs);
        $form = $this->createForm(InputsFarmType::class, $inputsFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($inputsFarm);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_plan_show', ['id' => $inputs->getId()]);
        }

        return $this->render('inputs_farm/new.html.twig', [
            'inputs_farm' => $inputsFarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="inputs_farm_show", methods={"GET"})
     */
    public function show(InputsFarm $inputsFarm): Response
    {
        return $this->render('inputs_farm/show.html.twig', [
            'inputs_farm' => $inputsFarm,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="inputs_farm_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function edit(Request $request, InputsFarm $inputsFarm): Response
    {
        $form = $this->createForm(InputsFarmType::class, $inputsFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_show', [
                'id' => $inputsFarm->getEggInput()->getId(),
            ]);
        }

        return $this->render('inputs_farm/edit.html.twig', [
            'inputs_farm' => $inputsFarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="inputs_farm_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, InputsFarm $inputsFarm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inputsFarm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inputsFarm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('inputs_farm_index');
    }

    /**
     * @Route("/plan_delete/{id}", name="inputs_farm_plan_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deletePlan(Request $request, InputsFarm $inputsFarm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inputsFarm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inputsFarm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_plan_show', [
            'id' => $inputsFarm->getEggInput()->getId()
        ]);
    }
}
