<?php

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class Handler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $email = \mb_strtolower($command->email);

        if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            throw new \DomainException('User with this email is already registered.');
        }

        $user = new User(
            Uuid::uuid4()->toString(),
            $email,
            \password_hash($command->password, PASSWORD_DEFAULT)
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}
