<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SignUpController extends AbstractController
{
    private UserFetcher $users;
    private ErrorHandler $errors;

    public function __construct(UserFetcher $users, ErrorHandler $errors)
    {
        $this->users = $users;
        $this->errors = $errors;
    }

    /**
     * @Route("/signup", name="auth.signup")
     */
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');

                return $this->redirectToRoute('app_login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup.confirm")
     */
    public function confirm(
        Request $request,
        string $token,
        SignUp\Confirm\ByToken\Handler $handler,
        UserProviderInterface $provider,
        UserAuthenticatorInterface $authenticator,
        LoginFormAuthenticator $formAuthenticator
    ): Response {
        if (null === $user = $this->users->findBySignUpConfirmToken($token)) {
            $this->addFlash('error', 'Invalid or confirmed token.');

            return $this->redirectToRoute('auth.signup');
        }

        $command = new SignUp\Confirm\ByToken\Command($token);

        try {
            $handler->handle($command);

            return $authenticator->authenticateUser(
                $provider->loadUserByIdentifier($user->email),
                $formAuthenticator,
                $request
            );
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('auth.signup');
        }
    }
}
