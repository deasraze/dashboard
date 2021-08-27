<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class HomeTest extends WebTestCase
{
    public function testGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testUser(): void
    {
        $client = static::createClient();

        $user = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::userIdentifier());

        $client->loginUser($user);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }

    public function testAdmin(): void
    {
        $client = static::createClient();

        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $client->loginUser($admin);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }
}
