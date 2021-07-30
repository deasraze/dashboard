<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use App\Security\UserProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GithubAuthenticator extends OAuth2Authenticator
{
    private UrlGeneratorInterface $urlGenerator;
    private ClientRegistry $clientRegistry;
    private UserProvider $provider;
    private Handler $handler;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ClientRegistry $clientRegistry,
        UserProvider $provider,
        Handler $handler
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->clientRegistry = $clientRegistry;
        $this->provider = $provider;
        $this->handler = $handler;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'oauth.github_check';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $client = $this->clientRegistry->getClient('github');
        /* @var GithubResourceOwner $githubUser */
        $githubUser = $client->fetchUserFromToken($this->fetchAccessToken($client));

        $network = 'github';
        $id = (string) $githubUser->getId();
        $username = $network . ':' . $id;

        $command = new Command($network, $id);
        $command->firstName = $githubUser->getName();
        $command->lastName = $githubUser->getNickname();

        return new SelfValidatingPassport(
            new UserBadge($username, function ($identifier) use ($command) {
                try {
                    return $this->provider->loadUserByIdentifier($identifier);
                } catch (UserNotFoundException $e) {
                    $this->handler->handle($command);

                    return $this->provider->loadUserByIdentifier($identifier);
                }
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
