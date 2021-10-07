<?php

namespace App\Controller;

use App\Entity\PlanInput;
use App\Entity\PlanInputFarm;
use App\Form\PlanInputFarmType;
use App\Repository\PlanInputFarmRepository;
use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan_input_farm")
 */
class PlanInputFarmController extends AbstractController
{
    public function sendSMS($message)
    {
        $sms = (new SmsapiHttpClient())
            ->smsapiPlService('RrmJehJczn7ujKurM4IenLKrhzeITeZfkPWID7ue')
            ->smsFeature()
            ->sendSms(SendSmsBag::withMessage('48669905464', $message));

    }

    /**
     * @Route("/new/{id}", name="plan_input_farm_new", methods={"GET","POST"})
     */
    public function new(Request $request, PlanInput $planInput): Response
    {
        $planInputFarm = new PlanInputFarm();
        $planInputFarm->setEggInput($planInput);
        $form = $this->createForm(PlanInputFarmType::class, $planInputFarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planInputFarm);

            $message = 'Zaplanowano nakład na dzień '
                . $planInputFarm->getEggInput()->getInputDate()->format('Y-m-d')
                . ' ferma: '
                . $planInputFarm->getChicksFarm()->getName()
                . ' ilość piskląt: '
                . $planInputFarm->getChickNumber();

            $entityManager->flush();
            $this->sendSMS($message);

            return $this->redirectToRoute('plan_input_show', ['id' => $planInput->getId()]);
        }

        return $this->render('plan_input_farm/new.html.twig', [
            'plan_input_farm' => $planInputFarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_input_farm_delete", methods={"POST"})
     */
    public function delete(Request $request, PlanInputFarm $planInputFarm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planInputFarm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planInputFarm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_input_farm_index');
    }
}
