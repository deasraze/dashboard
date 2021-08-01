<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\ReadModel\User\UserFetcher;
use App\Security\UserIdentity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testSuccess(): void
    {
        $client = static::createClient();
        $fetcher = static::getContainer()->get(UserFetcher::class);

        $testUser = $fetcher->findForAuthByEmail('admin@app.test');

        $client->loginUser(new UserIdentity(
            $testUser->id,
            $testUser->email,
            $testUser->password_hash,
            $testUser->name,
            $testUser->role,
            $testUser->status
        ));

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
    }
}
