<?php

namespace App\Controller;

use App\Entity\EggsInputs;
use App\Form\EggsInputsType;
use App\Repository\EggsInputsDetailsRepository;
use App\Repository\EggsInputsRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/eggs_inputs")
 * @IsGranted("ROLE_USER")
 */
class EggsInputsController extends AbstractController
{
    /**
     * @Route("/", name="eggs_inputs_index", methods={"GET"})
     */
    public function index(EggsInputsRepository $eggsInputsRepository): Response
    {
        return $this->render('eggs_inputs/index.html.twig', [
            'eggs_inputs' => $eggsInputsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="eggs_inputs_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $eggsInput = new EggsInputs();
        $form = $this->createForm(EggsInputsType::class, $eggsInput);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eggsInput);
            $entityManager->flush();

            return $this->redirectToRoute('eggs_inputs_index');
        }

        return $this->render('eggs_inputs/new.html.twig', [
            'eggs_input' => $eggsInput,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_show", methods={"GET"})
     */
    public function show(EggsInputs $eggsInput, EggsInputsDetailsRepository $detailsRepository): Response
    {
        $details = $detailsRepository->deliveriesInput($eggsInput);
        return $this->render('eggs_inputs/show.html.twig', [
            'eggs_input' => $eggsInput,
            'details_input' => $details,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eggs_inputs_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EggsInputs $eggsInput): Response
    {
        $form = $this->createForm(EggsInputsType::class, $eggsInput);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eggs_inputs_index');
        }

        return $this->render('eggs_inputs/edit.html.twig', [
            'eggs_input' => $eggsInput,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eggs_inputs_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EggsInputs $eggsInput): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eggsInput->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eggsInput);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eggs_inputs_index');
    }
}
