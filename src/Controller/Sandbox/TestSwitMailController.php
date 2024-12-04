<?php

namespace App\Controller\Sandbox;

use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class TestSwitMailController extends AbstractController
{

        public function __construct (private Mailer $mailer) {

        }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/test/swit/mail', name: 'app_test_swit_mail')]
    public function index(): Response
    {
        $user = $this->getUser();
        $this->mailer->sendConfirmationMessage($user);
        //dd($res);

        return $this->render('test_swit_mail/index.html.twig', [
            'controller_name' => 'TestSwitMailController',
        ]);
    }
}
