<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Role;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthFixture extends Fixture
{
    public const REFERENCE_ADMIN = 'test_auth_admin';
    public const REFERENCE_USER = 'test_auth_user';

    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hashing('password');

        $user = (new UserBuilder())
            ->viaEmail(new Email('auth-user@app.test'), $hash)
            ->confirmed()
            ->build();

        $manager->persist($user);
        $this->addReference(self::REFERENCE_USER, $user);

        $admin = (new UserBuilder())
            ->viaEmail(new Email('auth-admin@app.test'), $hash)
            ->confirmed()
            ->withRole(Role::admin())
            ->build();

        $manager->persist($admin);
        $this->addReference(self::REFERENCE_ADMIN, $admin);

        $manager->flush();
    }
}
