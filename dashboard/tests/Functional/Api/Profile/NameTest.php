<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Profile;

use App\Model\User\Entity\User\Id;
use App\Tests\Functional\DbWebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class NameTest extends DbWebTestCase
{
    use ArraySubsetAsserts;

    private const URI = '/api/profile/name';

    protected function setUp(): void
    {
        parent::setUp();

        $this->client->setServerParameters(\array_merge(
            ProfileFixture::userCredentials(),
            ['CONTENT_TYPE' => 'application/json']
        ));
    }

    public function testGet(): void
    {
        $this->client->request('GET', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testPost(): void
    {
        $this->client->request('POST', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testPut(): void
    {
        $this->client->request('PUT', self::URI, [], [], [], \json_encode([
            'id' => Id::next(), // fake id
            'first' => 'Zelda',
            'last' => 'Ones',
        ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertEquals([], $data);

        $this->client->request('GET', '/api/profile');
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertArraySubset([
            'name' => [
                'first' => 'Zelda',
                'last' => 'Ones',
            ],
        ], $data);
    }

    public function testNotValid(): void
    {
        $this->client->request('PUT', self::URI, [], [], [], \json_encode([]));

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertArraySubset([
            'violations' => [
                ['propertyPath' => 'first', 'title' => 'This value should not be blank.'],
                ['propertyPath' => 'last', 'title' => 'This value should not be blank.'],
            ],
        ], $data);
    }
}
