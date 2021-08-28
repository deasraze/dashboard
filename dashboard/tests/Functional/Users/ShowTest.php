<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Model\User\Entity\User\Id;
use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShowTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/users/' . UsersFixture::EXISTING_ID);

        $this->assertResponseRedirects('/login', 302);
    }

    public function testUser(): void
    {
        $user = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::userIdentifier());

        $this->client->loginUser($user);
        $this->client->request('GET', '/users/' . UsersFixture::EXISTING_ID);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGet(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);
        $this->client->request('GET', '/users/' . UsersFixture::EXISTING_ID);

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Users');
        $this->assertSelectorTextContains('table', 'User Show');
    }

    public function testNotFound(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);
        $this->client->request('GET', '/users/' . Id::next()->getValue());

        $this->assertResponseStatusCodeSame(404);
    }
}
