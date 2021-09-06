<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Profile;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public const USER_ID = '00000000-0000-0000-0000-100000000001';

    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public static function userCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'profile-user@app.test',
            'PHP_AUTH_PW' => 'password',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $user = (new UserBuilder())
            ->viaEmail(new Email('profile-user@app.test'), $this->hasher->hashing('password'))
            ->withId(new Id(self::USER_ID))
            ->withName(new Name('Profile', 'User'))
            ->confirmed()
            ->build();

        $user->attachNetwork('vk', '2222');

        $manager->persist($user);

        $manager->flush();
    }
}
