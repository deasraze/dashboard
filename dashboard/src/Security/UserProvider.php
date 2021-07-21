<?php

declare(strict_types=1);

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

        return self::identityByUser($user, $identifier);
    }

    public function loadUserByUsername(string $username)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException(\sprintf('Invalid user class "%s".', \get_class($user)));
        }

        $identifier = $user->getUserIdentifier();
        $user = $this->loadUser($identifier);

        return self::identityByUser($user, $identifier);
    }

    public function supportsClass(string $class): bool
    {
        return (UserIdentity::class === $class || \is_subclass_of($class, UserIdentity::class));
    }

    private function loadUser(string $identifier): AuthView
    {
        $chunks = \explode(':', $identifier);

        if (\count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($identifier)) {
            return $user;
        }

        throw new UserNotFoundException('');
    }

    private static function identityByUser(AuthView $user, string $username): UserInterface
    {
        return new UserIdentity(
            $user->id,
            $username,
            $user->password_hash ?: '',
            $user->role,
            $user->status
        );
    }
}
