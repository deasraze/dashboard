<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class IndexTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/users');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testUser(): void
    {
        $user = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::userIdentifier());

        $this->client->loginUser($user);
        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdmin(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);
        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Users');
    }
}
