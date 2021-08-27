<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Tests\Functional\AuthFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class IndexTest extends WebTestCase
{
    public function testGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testUser(): void
    {
        $client = static::createClient();
        $user = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::userIdentifier());

        $client->loginUser($user);

        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdmin(): void
    {
        $client = static::createClient();
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $client->loginUser($admin);

        $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Users');
    }
}
