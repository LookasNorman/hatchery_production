<?php

use App\Entity\ContactInfo;
use App\Entity\Delivery;
use App\Entity\Hatchers;
use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use App\Entity\Supplier;
use App\Form\DeliveryProductionType;
use App\Form\InputDeliveryProductionType;
use App\Repository\InputsRepository;
use App\Repository\SupplierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/production/delivery")
 * @IsGranted("ROLE_PRODUCTION")
 */

class ProductionDeliveryController
{

}
