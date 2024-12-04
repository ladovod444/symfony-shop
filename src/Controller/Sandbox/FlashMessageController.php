<?php

namespace App\Controller\Sandbox;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FlashMessageController extends AbstractController
{
    #[Route('/flash/message', name: 'app_flash_message')]
    public function index(): Response
    {
        return $this->render('flash_message/index.html.twig', [
            'controller_name' => 'FlashMessageController',
        ]);
    }
}
