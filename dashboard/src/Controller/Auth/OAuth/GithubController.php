<?php

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{
    /**
     * @Route("/oauth/github", name="oauth.github")
     */
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect(['user'], []);
    }

    /**
     * @Route("/oauth/github/check", name="oauth.github_check")
     */
    public function check(): Response
    {
        return $this->redirectToRoute('home');
    }
}
