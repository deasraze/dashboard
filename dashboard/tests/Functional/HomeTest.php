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
        $provider = static::getContainer()->get(UserProviderInterface::class);

        $client->loginUser($provider->loadUserByIdentifier('auth-user@app.test'));

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }

    public function testAdmin(): void
    {
        $client = static::createClient();
        $provider = static::getContainer()->get(UserProviderInterface::class);

        $client->loginUser($provider->loadUserByIdentifier('auth-admin@app.test'));

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }
}
