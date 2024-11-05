<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\RegisteredUserEvent;
use App\Form\RegistrationFormType;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use Composer\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        CodeGenerator               $codeGenerator,
        Mailer                      $mailer,
      EventDispatcherInterface     $eventDispatcher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set confirmation code
            $user->setConfirmationCode($codeGenerator->getConfirmationCode());

            $entityManager->persist($user);
            $entityManager->flush();

            // Send mail
            //$mailer->sendConfirmationMessage($user);
            
            $registerUserEvent = new RegisteredUserEvent($user);
            $eventDispatcher->dispatch($registerUserEvent, RegisteredUserEvent::NAME);

            // do anything else you need here, like send an email
            $this->addFlash('success', 'You have been registered!');
            return $this->redirectToRoute('app_default');
            //return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
