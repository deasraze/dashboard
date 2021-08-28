<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\Security\Core\User\UserProviderInterface;

class HomeTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testUser(): void
    {
        $user = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::userIdentifier());

        $this->client->loginUser($user);
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }

    public function testAdmin(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }
}
