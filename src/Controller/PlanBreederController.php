<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanBreederController extends AbstractController
{
    /**
     * @Route("/plan/breeder", name="plan_breeder")
     */
    public function index(): Response
    {
        return $this->render('plan_breeder/index.html.twig', [
            'controller_name' => 'PlanBreederController',
        ]);
    }
}
