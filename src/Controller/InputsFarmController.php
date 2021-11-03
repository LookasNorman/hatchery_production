<?php

namespace App\Controller;

use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Form\InputsFarmType;
use App\Repository\InputsFarmRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function new(Request $request, Inputs $inputs = null): Response
    {
        $inputsFarm = new InputsFarm();
        $inputsFarm->setEggInput($inputs);
        $form = $this->createForm(InputsFarmType::class, $inputsFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($inputsFarm);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_show', ['id' => $inputs->getId()]);
        }

        return $this->render('inputs_farm/new.html.twig', [
            'inputs_farm' => $inputsFarm,
            'form' => $form->createView(),
        ]);
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

            return $this->redirectToRoute('inputs_farm_index');
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
