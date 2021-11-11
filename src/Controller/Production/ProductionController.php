<?php

namespace App\Controller\Production;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/production")
 * @IsGranted("ROLE_PRODUCTION")
 */
class ProductionController extends AbstractController
{

    /**
     * @Route("/", name="production_index")
     */
    public function productionSite(): Response
    {
        return $this->render('main_page/production.html.twig');
    }
}
