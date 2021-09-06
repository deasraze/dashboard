<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Profile;

use App\Tests\Functional\DbWebTestCase;

class ShowTest extends DbWebTestCase
{
    private const URI = '/api/profile';

    public function testGuest(): void
    {
        $this->client->request('GET', self::URI);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUser(): void
    {
        $this->client->setServerParameters(ProfileFixture::userCredentials());

        $this->client->request('GET', self::URI);

        $this->assertResponseIsSuccessful();
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = \json_decode($content, true);

        self::assertEquals([
            'id' => ProfileFixture::USER_ID,
            'email' => 'profile-user@app.test',
            'name' => [
                'first' => 'Profile',
                'last' => 'User',
            ],
            'networks' => [
                [
                    'name' => 'vk',
                    'identity' => '2222',
                ],
            ],
        ], $data);
    }
}
