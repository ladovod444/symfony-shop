<?php

namespace App\Event;

use App\Entity\Contacts;
use App\Entity\User;
//use Symfony\Component\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\Event;

class ContactsSendEvent extends Event
{
    public const NAME = 'contacts.send';

    /**
     * @var Contacts
     */
    private $contacts;

    /**
     * @param Contacts $contacts
     */
    public function __construct(Contacts $contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * @return Contacts
     */
    public function getContacts(): Contacts
    {
        return $this->contacts;
    }
}