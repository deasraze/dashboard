<?php

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use App\Security\UserProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
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
        $accessToken = $this->fetchAccessToken($client);

        $network = 'github';
        $id = $client->fetchUserFromToken($accessToken)->getId();
        $username = $network . ':' . $id;

        return new SelfValidatingPassport(
            new UserBadge($username, function ($identifier) use ($network, $id) {
                try {
                    return $this->provider->loadUserByIdentifier($identifier);
                } catch (UserNotFoundException $e) {
                    $this->handler->handle(new Command($network, $id));

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
