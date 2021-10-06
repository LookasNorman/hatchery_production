<?php

namespace App\Controller;

use App\Entity\InputsFarm;
use App\Form\InputsFarmType;
use App\Repository\InputsFarmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inputs/farm")
 */
class InputsFarmController extends AbstractController
{
    /**
     * @Route("/", name="inputs_farm_index", methods={"GET"})
     */
    public function index(InputsFarmRepository $inputsFarmRepository): Response
    {
        return $this->render('inputs_farm/index.html.twig', [
            'inputs_farms' => $inputsFarmRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="inputs_farm_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $inputsFarm = new InputsFarm();
        $form = $this->createForm(InputsFarmType::class, $inputsFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($inputsFarm);
            $entityManager->flush();

            return $this->redirectToRoute('inputs_farm_index');
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
}
