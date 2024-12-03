<?php

namespace App\Service;

use App\Contacts\Contacts;
use App\Entity\Contacts as ContactsEntity;
use App\Event\ContactsSendEvent;
use App\Repository\ContactsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ContactsService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createContacts(Contacts $contacts): ContactsEntity
    {
        $contacts_entity = new ContactsEntity();
        $contacts_entity->setTitle($contacts->getTitle())
            ->setEmail($contacts->getEmail())
            ->setBody($contacts->getBody());
        $this->entityManager->persist($contacts_entity);

        $this->entityManager->flush();

        
        // Отправить слушателям событие о создании контактов
        $event = new ContactsSendEvent($contacts_entity);
        $this->eventDispatcher->dispatch($event, ContactsSendEvent::NAME);

        return $contacts_entity;
    }
}