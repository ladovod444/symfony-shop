<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailConfirmationController extends AbstractController
{
    public function __construct(private UserRepository $userRepository,
    private EntityManagerInterface $entityManager)
    {

    }
    #[Route('/email/confirmation/{code}', name: 'email_confirmation')]
    public function index(string $code): Response
    {
        //Получить юзера по этому коду
        $user = $this->userRepository->findOneBy(['confirmationCode' => $code]);
        //dd($user);
        if ($user === null) {
            return new Response('404');
        }

        $user->setEnabled(true);
        $user->setConfirmationCode('');

        $this->entityManager->flush();
        // Обновить enabled на true
        // и редиректить на login

        return $this->render('email_confirmation/index.html.twig', [
            'controller_name' => 'EmailConfirmationController',
            'code' => $code,
        ]);
    }
}
