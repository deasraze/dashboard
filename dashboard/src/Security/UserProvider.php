<?php

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserFetcher $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->loadUser($identifier);

        return self::identityByUser($user);
    }

    public function loadUserByUsername(string $username)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException(\sprintf('Invalid user class "%s".', \get_class($user)));
        }

        $user = $this->loadUser($user->getUserIdentifier());

        return self::identityByUser($user);
    }

    public function supportsClass(string $class): bool
    {
        return (UserIdentity::class === $class || \is_subclass_of($class, UserIdentity::class));
    }

    private function loadUser(string $identifier): AuthView
    {
        $user = $this->users->findForAuth($identifier);

        if (null === $user) {
            throw new UserNotFoundException('');
        }

        return $user;
    }

    private static function identityByUser(AuthView $user): UserInterface
    {
        return new UserIdentity(
            $user->id,
            $user->email,
            $user->password_hash,
            $user->role,
            $user->status
        );
    }
}
