<?php

namespace App\Controller;

use App\Entity\ChickTemperature;
use App\Entity\Hatchers;
use App\Form\HatchersType;
use App\Repository\HatchersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/hatchers")
 * @IsGranted("ROLE_USER")
 */
class HatchersController extends AbstractController
{
    /**
     * @Route("/", name="hatchers_index", methods={"GET"})
     */
    public function index(HatchersRepository $hatchersRepository): Response
    {
        return $this->render('hatchers/index.html.twig', [
            'hatchers' => $hatchersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="hatchers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $hatcher = new Hatchers();
        $form = $this->createForm(HatchersType::class, $hatcher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hatcher);
            $entityManager->flush();

            return $this->redirectToRoute('hatchers_index');
        }

        return $this->render('hatchers/new.html.twig', [
            'hatcher' => $hatcher,
            'form' => $form->createView(),
        ]);
    }

    public function getChickTemperatureInHatcher(Hatchers $hatcher)
    {
        $chickTemperatureRepository = $this->getDoctrine()->getRepository(ChickTemperature::class);
        $chickTemperatures = $chickTemperatureRepository->findBy(['hatcher' => $hatcher],['date' => 'DESC']);
        return $chickTemperatures;
    }

    /**
     * @Route("/{id}", name="hatchers_show", methods={"GET"})
     */
    public function show(Hatchers $hatcher): Response
    {
        $chickTemperatires = $this->getChickTemperatureInHatcher($hatcher);

        return $this->render('hatchers/show.html.twig', [
            'hatcher' => $hatcher,
            'chick_temperatures' => $chickTemperatires
        ]);
    }

    /**
     * @Route("/{id}/edit", name="hatchers_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Hatchers $hatcher): Response
    {
        $form = $this->createForm(HatchersType::class, $hatcher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hatchers_index');
        }

        return $this->render('hatchers/edit.html.twig', [
            'hatcher' => $hatcher,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hatchers_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Hatchers $hatcher): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hatcher->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hatcher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('hatchers_index');
    }
}
