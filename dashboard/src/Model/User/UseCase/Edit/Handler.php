<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Edit;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->email);

        if (!$user->getEmail()->isEqualTo($email) && $this->users->hasByEmail($email)) {
            throw new \DomainException('Email is already used.');
        }

        $user->edit(
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email
        );

        $this->flusher->flush();
    }
}
