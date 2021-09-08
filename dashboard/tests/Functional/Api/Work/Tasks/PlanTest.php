<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Work\Tasks;

use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class PlanTest extends DbWebTestCase
{
    use ArraySubsetAsserts;

    private const URI = '/api/work/projects/tasks/%s/plan';
    private const SHOW_URI = '/api/work/projects/tasks/%s';

    protected function setUp(): void
    {
        parent::setUp();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    public function testGet(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));

        $this->assertResponseStatusCodeSame(405);
    }

    public function testPost(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('POST', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));

        $this->assertResponseStatusCodeSame(405);
    }

    public function testAdmin(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());

        $date = new \DateTimeImmutable('+1 day');

        $this->client->request('PUT', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER), [], [], [], \json_encode([
            'date' => $date->format('Y-m-d'),
        ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertEquals([], $data);

        $this->client->request('GET', \sprintf(self::SHOW_URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertArraySubset([
            'plan_date' => $date->format('Y-m-d'),
        ], $data);
    }

    public function testMember(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());

        $date = new \DateTimeImmutable('+1 day');

        $this->client->request('PUT', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER), [], [], [], \json_encode([
            'date' => $date->format('Y-m-d'),
        ]));

        $this->assertResponseIsSuccessful();
    }

    public function testNotMember(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());

        $date = new \DateTimeImmutable('+1 day');

        $this->client->request('PUT', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITHOUT_USER), [], [], [], \json_encode([
            'date' => $date->format('Y-m-d'),
        ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testNotValid(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());
        $this->client->request('PUT', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertArraySubset(['detail' => 'Date field is required.'], $data);
    }
}
