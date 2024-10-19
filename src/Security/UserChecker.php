<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!$user->getEnabled()) {
            throw new CustomUserMessageAuthenticationException(
                'Inactive account cannot log in'
            );
        }
        return true;
    }

    public function checkPostAuth(UserInterface $user): bool
    {
        return $this->checkPreAuth($user);
    }
}