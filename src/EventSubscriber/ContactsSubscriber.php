<?php

namespace App\EventSubscriber;

use App\Event\ContactsSendEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\EventSubscriber;

class ContactsSubscriber implements EventSubscriberInterface
{

    public function __construct(private Mailer $mailer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactsSendEvent::NAME => 'onContactsSend',
        ];
    }

    public function onContactsSend(ContactsSendEvent $event): void
    {

        $contacts = $event->getContacts();
        $this->mailer->sendContactsMessage($contacts);

    }
}