<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Auth;

use App\Tests\Functional\DbWebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class SignUpTest extends DbWebTestCase
{
    use ArraySubsetAsserts;

    private const URI = '/api/auth/signup';

    protected function setUp(): void
    {
        parent::setUp();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    public function testGet(): void
    {
        $this->client->request('GET', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $this->client->request('POST', self::URI, [], [], [], \json_encode([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test-john@app.test',
            'password' => 'password',
        ]));

        $this->assertResponseStatusCodeSame(201);
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = \json_decode($content, true);

        self::assertEquals([], $data);
    }

    public function testNotValid(): void
    {
        $this->client->request('POST', self::URI, [], [], [], \json_encode([
            'first_name' => '',
            'last_name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ]));

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = \json_decode($content, true);

        self::assertArraySubset([
            'violations' => [
                ['propertyPath' => 'first_name', 'title' => 'This value should not be blank.'],
                ['propertyPath' => 'last_name', 'title' => 'This value should not be blank.'],
                ['propertyPath' => 'email', 'title' => 'This value is not a valid email address.'],
                ['propertyPath' => 'password', 'title' => 'This value is too short. It should have 6 characters or more.'],
            ],
        ], $data);
    }

    public function testExists(): void
    {
        $this->client->request('POST', self::URI, [], [], [], \json_encode([
            'first_name' => 'Tom',
            'last_name' => 'Aiz',
            'email' => 'existing-user@app.test',
            'password' => 'password',
        ]));

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = \json_decode($content, true);

        self::assertArraySubset([
            'error' => [
                'message' => 'User with this email is already registered.',
            ],
        ], $data);
    }
}
