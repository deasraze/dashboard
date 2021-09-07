<?php

declare(strict_types=1);

namespace App\Event\Listener\OAuth\Server;

use App\Model\User\Service\PasswordHasher;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

final class UserResolveListener
{
    private UserProviderInterface $provider;
    private PasswordHasher $hasher;

    public function __construct(UserProviderInterface $provider, PasswordHasher $hasher)
    {
        $this->provider = $provider;
        $this->hasher = $hasher;
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        $user = $this->provider->loadUserByIdentifier($event->getUsername());

        if (null === $user) {
            return;
        }

        if (!$user->getPassword()) {
            return;
        }

        if (!$this->hasher->verify($event->getPassword(), $user->getPassword())) {
            return;
        }

        $event->setUser($user);
    }
}
