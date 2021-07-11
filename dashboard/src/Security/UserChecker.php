<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof UserIdentity) {
            return;
        }

        if (!$user->isActive()) {
            $exception = new DisabledException('User account is not active.');
            $exception->setUser($user);

            throw $exception;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof UserIdentity) {
            return;
        }
    }
}
