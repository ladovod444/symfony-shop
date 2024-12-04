<?php

namespace App\Controller\Sandbox;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QuestController extends AbstractController
{
    #[Route('/quest', name: 'app_quest')]
    public function index(): Response
    {

        $order_type = $this->getParameter('app:retailcrm:order_type');
        dd($order_type);
        return $this->render('quest/index.html.twig', [
            'controller_name' => 'QuestController',
        ]);
    }
}
