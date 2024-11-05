<?php

namespace App\EventSubscriber;

use App\Event\RegisteredUserEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

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
          RegisteredUserEvent::NAME => 'onUserRegister'
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
}