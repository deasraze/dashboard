<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Work\Entity\Projects\Task\Executor;

use App\Tests\Builder\Work\Members\GroupBuilder;
use App\Tests\Builder\Work\Members\MemberBuilder;
use App\Tests\Builder\Work\Projects\ProjectBuilder;
use App\Tests\Builder\Work\Projects\TaskBuilder;
use PHPUnit\Framework\TestCase;

class RevokeTest extends TestCase
{
    public function testSuccess(): void
    {
        $memberBuilder = new MemberBuilder();

        $group = (new GroupBuilder())->build();
        $member = $memberBuilder->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $executor = $memberBuilder->build($group);

        $task->assignExecutor($member, new \DateTimeImmutable(), $executor);

        self::assertTrue($task->hasExecutor($executor->getId()));

        $task->revokeExecutor($member, new \DateTimeImmutable(), $executor->getId());

        self::assertEquals([], $task->getExecutors());
        self::assertFalse($task->hasExecutor($executor->getId()));
    }

    public function testNotFound(): void
    {
        $memberBuilder = new MemberBuilder();

        $group = (new GroupBuilder())->build();
        $member = $memberBuilder->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $executor = $memberBuilder->build($group);

        $this->expectExceptionMessage('Executor is not assigned.');
        $task->revokeExecutor($member, new \DateTimeImmutable(), $executor->getId());
    }
}
