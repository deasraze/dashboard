<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hashing('password');

        $network = $this->createSignedUpByNetwork(
            new Name('Adam', 'Smith'),
            'vk',
            '11111'
        );
        $manager->persist($network);

        $requested = $this->createSignUpRequestedByEmail(
            new Name('Jessica', 'Alen'),
            new Email('jessica@app.test'),
            $hash
        );
        $manager->persist($requested);

        $confirmed = $this->createSignUpConfirmedByEmail(
            new Name('Brad', 'Pitt'),
            new Email('brad.pitt@app.test'),
            $hash
        );
        $manager->persist($confirmed);

        $admin = $this->createAdminByEmail(
            new Name('Artur', 'Roze'),
            new Email('admin@app.test'),
            $hash
        );
        $manager->persist($admin);

        $manager->flush();
    }

    private function createAdminByEmail(Name $name, Email $email, string $hash): User
    {
        $user = $this->createSignUpConfirmedByEmail($name, $email, $hash);

        $user->changeRole(Role::admin());

        return $user;
    }

    private function createSignUpConfirmedByEmail(Name $name, Email $email, string $hash): User
    {
        $user = $this->createSignUpRequestedByEmail($name, $email, $hash);

        $user->confirmSignUp();

        return $user;
    }

    private function createSignUpRequestedByEmail(Name $name, Email $email, string $hash): User
    {
        return User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $name,
            $email,
            $hash,
            'token'
        );
    }

    private function createSignedUpByNetwork(Name $name, string $network, string $identity): User
    {
        return User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            $name,
            $network,
            $identity
        );
    }
}
