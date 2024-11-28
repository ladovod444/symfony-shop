<?php

namespace App\EventSubscriber;

use App\Event\RegisteredUserEvent;
use App\Service\Mailer;
use Composer\Util\Http\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Lexik\Bundle\JWTAuthenticationBundle\Events;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RegisteredUserEvent::NAME => 'onUserRegister',
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    /**
     * @param RegisteredUserEvent $registeredUserEvent
     *
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax*@throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function onUserRegister(RegisteredUserEvent $registeredUserEvent): void
    {
        $this->mailer->sendConfirmationMessage($registeredUserEvent->getRegisteredUser());
    }

    /**
     * Listen to Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent
     * to add user id to login response.
     *
     * @param AuthenticationSuccessEvent $event
     * @return void
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {

        $data = $event->getData();
        if ($data['token']) {
            $user = $event->getUser();
            $data['id'] = $user->getId();
            //$data['username'] = $user->getEmail();

            $event->setData($data);
        }
    }
}