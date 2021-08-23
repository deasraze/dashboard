<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\Name\Command;
use App\Model\User\UseCase\Name\Form;
use App\Model\User\UseCase\Name\Handler;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NameController extends AbstractController
{
    private UserFetcher $users;
    private ErrorHandler $errors;

    public function __construct(UserFetcher $users, ErrorHandler $errors)
    {
        $this->users = $users;
        $this->errors = $errors;
    }

    /**
     * @Route("profile/name", name="profile.name")
     */
    public function request(Request $request, Handler $handler): Response
    {
        $user = $this->users->get($this->getUser()->getId());

        $command = Command::fromUser($user);

        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Name is successfully changed.');

                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/name.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
