<?php

namespace App\Controller;

use App\Entity\TransportInputsFarm;
use App\Form\TransportInputsFarmTimeType;
use App\Form\TransportInputsFarmType;
use App\Repository\TransportInputsFarmRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transport_inputs_farm")
 * @IsGranted("ROLE_USER")
 */
class TransportInputsFarmController extends AbstractController
{

    /**
     * @Route("/{id}/edit", name="transport_inputs_farm_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_TRANSPORT")
     */
    public function edit(Request $request, TransportInputsFarm $transportInputsFarm): Response
    {
        $input = $transportInputsFarm->getFarm()->getEggInput();
        $form = $this->createForm(TransportInputsFarmTimeType::class, $transportInputsFarm, [
            'input' => $input
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transport_list_show', [
                'id' => $transportInputsFarm->getTransportList()->getId()
            ]);
        }

        return $this->render('transport_inputs_farm/edit.html.twig', [
            'transport_inputs_farm' => $transportInputsFarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_inputs_farm_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, TransportInputsFarm $transportInputsFarm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transportInputsFarm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transportInputsFarm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transport_inputs_farm_index');
    }
}
