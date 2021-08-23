<?php

declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\Network;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/oauth/github")
 */
class GithubController extends AbstractController
{
    private ErrorHandler $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/attach", name="profile.oauth.github")
     */
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('github_attach')
            ->redirect(['user'], []);
    }

    /**
     * @Route("/check", name="profile.oauth.github_check")
     */
    public function check(ClientRegistry $clientRegistry, Network\Attach\Handler $handler): Response
    {
        $identity = (string) $clientRegistry->getClient('github_attach')
            ->fetchUser()
            ->getId();

        $command = new  Network\Attach\Command(
            $this->getUser()->getId(),
            'github',
            $identity
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'GitHub is successfully attached.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('profile');
    }
}
