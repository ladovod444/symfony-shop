<?php

namespace App\Event;

use App\Entity\User;
//use Symfony\Component\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\Event;

class RegisteredUserEvent extends Event
{
    public const NAME = 'user.register';

    /**
     * @var User
     */
    private $registeredUser;

    /**
     * @param User $registeredUser
     */
    public function __construct(User $registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return $this->registeredUser;
    }
}