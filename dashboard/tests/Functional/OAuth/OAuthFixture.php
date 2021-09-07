<?php

declare(strict_types=1);

namespace App\Tests\Functional\OAuth;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Grants;

class OAuthFixture extends Fixture
{
    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = (new UserBuilder())
            ->viaEmail(new Email('oauth-password-user@app.test'), $this->hasher->hashing('password'))
            ->withName(new Name('Oauth', 'User'))
            ->confirmed()
            ->build();

        $manager->persist($user);

        $client = new Client('oauth', 'secret');
        $client->setGrants(new Grant(OAuth2Grants::PASSWORD));

        $manager->persist($client);

        $manager->flush();
    }
}
