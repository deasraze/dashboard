<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/users/create');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testUser(): void
    {
        $user = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::userIdentifier());

        $this->client->loginUser($user);
        $this->client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGet(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);
        $this->client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Users');
    }

    public function testCreate()
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);

        $this->client->request('GET', '/users/create');
        $this->client->submitForm('Create', [
            'form[firstName]' => 'Bob',
            'form[lastName]' => 'Olin',
            'form[email]' => 'bob-olin@app.test',
        ]);

        $this->assertResponseRedirects('/users', 302);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Users');
        $this->assertSelectorTextContains('body', 'Olin Bob');
        $this->assertSelectorTextContains('body', 'bob-olin@app.test');
    }

    public function testNotValid(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);
        $this->client->request('GET', '/users/create');

        $crawler = $this->client->submitForm('Create', [
            'form[firstName]' => '',
            'form[lastName]' => '',
            'form[email]' => 'not-email',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString(
            'This value should not be blank.',
            $crawler->filter('#form_firstName')->ancestors()->first()->filter('.form-error-message')->text()
        );
        $this->assertStringContainsString(
            'This value should not be blank.',
            $crawler->filter('#form_lastName')->ancestors()->first()->filter('.form-error-message')->text()
        );
        $this->assertStringContainsString(
            'This value is not a valid email address.',
            $crawler->filter('#form_email')->ancestors()->first()->filter('.form-error-message')->text()
        );
    }

    public function testExists(): void
    {
        $admin = static::getContainer()
            ->get(UserProviderInterface::class)
            ->loadUserByIdentifier(AuthFixture::adminIdentifier());

        $this->client->loginUser($admin);

        $this->client->request('GET', '/users/create');
        $this->client->submitForm('Create', [
            'form[firstName]' => 'Bob',
            'form[lastName]' => 'Olin',
            'form[email]' => 'existing-user@app.test',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert.alert-danger', 'Email is already used.');
    }
}
