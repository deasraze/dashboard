<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Work\Tasks;

use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class RemovePlanTest extends DbWebTestCase
{
    use ArraySubsetAsserts;

    private const URI = '/api/work/projects/tasks/%s/plan';
    private const SHOW_URI = '/api/work/projects/tasks/%s';

    public function testAdmin(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('DELETE', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));

        $this->assertResponseStatusCodeSame(204);

        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', \sprintf(self::SHOW_URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));

        $this->assertResponseIsSuccessful();
        self::assertJson($content = $this->client->getResponse()->getContent());
        $data = \json_decode($content, true);

        self::assertArraySubset([
            'plan_date' => null,
        ], $data);
    }

    public function testMember(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());
        $this->client->request('DELETE', \sprintf(self::URI, TaskFixture::TASK_IN_PROJECT_WITH_USER));

        $this->assertResponseStatusCodeSame(204);
    }
}
