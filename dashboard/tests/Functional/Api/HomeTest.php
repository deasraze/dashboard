<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Tests\Functional\DbWebTestCase;

class HomeTest extends DbWebTestCase
{
    public function testSuccess(): void
    {
        $this->client->request('GET', '/api');

        $this->assertResponseIsSuccessful();
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = \json_decode($content, true);

        self::assertEquals([
            'name' => 'JSON API',
        ], $data);
    }

    public function testPost(): void
    {
        $this->client->request('POST', '/api');

        $this->assertResponseStatusCodeSame(405);
    }
}
