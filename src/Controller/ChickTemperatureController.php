<?php

namespace App\Controller;

use App\Entity\ChickTemperature;
use App\Entity\Hatchers;
use App\Entity\Inputs;
use App\Form\ChickTemperatureType;
use App\Repository\ChickTemperatureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chick_temperature")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ChickTemperatureController extends AbstractController
{
    /**
     * @Route("/", name="chick_temperature_index", methods={"GET"})
     */
    public function index(ChickTemperatureRepository $chickTemperatureRepository): Response
    {
        $chickTemperature = $chickTemperatureRepository->findBy([], [
            'date' => 'DESC',
            'input' => 'ASC',
            'hatcher' => 'ASC'
        ]);

        return $this->render('chick_temperature/index.html.twig', [
            'chick_temperatures' => $chickTemperature,
        ]);
    }

    /**
     * @Route("/new/{input}/{hatcher}", name="chick_temperature_new", methods={"GET","POST"})
     */
    public function new(Request $request, Inputs $input = null, Hatchers $hatcher = null): Response
    {
        $chickTemperature = new ChickTemperature();
        $chickTemperature->setInput($input);
        $chickTemperature->setHatcher($hatcher);
        if ($input) {
            $date = $input->getInputDate();
            $date->modify('+21 day');
        } else {
            $date = new \DateTime();
        }
        $chickTemperature->setDate($date);
        $form = $this->createForm(ChickTemperatureType::class, $chickTemperature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chickTemperature);
            $entityManager->flush();

            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('chick_temperature_index');
            }

            if ($form->get('saveInput')->isClicked()) {
                return $this->redirectToRoute('chick_temperature_new', [
                    'input' => $chickTemperature->getInput()->getId(),
                ]);
            }

            return $this->redirectToRoute('chick_temperature_new', [
                'input' => $chickTemperature->getInput()->getId(),
                'hatcher' => $chickTemperature->getHatcher()->getId(),
            ]);

        }

        return $this->render('chick_temperature/new.html.twig', [
            'chick_temperature' => $chickTemperature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chick_temperature_show", methods={"GET"})
     */
    public function show(ChickTemperature $chickTemperature): Response
    {
        return $this->render('chick_temperature/show.html.twig', [
            'chick_temperature' => $chickTemperature,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chick_temperature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ChickTemperature $chickTemperature): Response
    {
        $form = $this->createForm(ChickTemperatureType::class, $chickTemperature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('chick_temperature_index');
        }

        return $this->render('chick_temperature/edit.html.twig', [
            'chick_temperature' => $chickTemperature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chick_temperature_delete", methods={"POST"})
     */
    public function delete(Request $request, ChickTemperature $chickTemperature): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chickTemperature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chickTemperature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chick_temperature_index');
    }
}
