<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Security\UserIdentity;
use phpcent\Client;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CentrifugoExtension extends AbstractExtension
{
    private Client $centrifugo;
    private Security $security;

    public function __construct(Client $centrifugo, Security $security)
    {
        $this->centrifugo = $centrifugo;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('centrifugo_token', [$this, 'token']),
        ];
    }

    public function token(): string
    {
        if (null === $user = $this->security->getUser()) {
            return '';
        }

        if (!$user instanceof UserIdentity) {
            return '';
        }

        return $this->centrifugo->generateConnectionToken($user->getId(), \time() + 3600 * 12);
    }
}
