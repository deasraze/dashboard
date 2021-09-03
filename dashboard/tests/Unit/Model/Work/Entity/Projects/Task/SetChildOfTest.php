<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\Members\GroupBuilder;
use App\Tests\Builder\Work\Members\MemberBuilder;
use App\Tests\Builder\Work\Projects\ProjectBuilder;
use App\Tests\Builder\Work\Projects\TaskBuilder;
use PHPUnit\Framework\TestCase;

class SetChildOfTest extends TestCase
{
    public function testSuccess(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();

        $taskBuilder = new TaskBuilder();

        $task = $taskBuilder->build($project, $member);
        $parent = $taskBuilder->build($project, $member);

        self::assertNull($task->getParent());

        $task->setChildOf($member, new \DateTimeImmutable(), $parent);

        self::assertEquals($parent, $task->getParent());
    }

    public function testSelf(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $this->expectExceptionMessage('Cyclomatic children.');
        $task->setChildOf($member, new \DateTimeImmutable(), $task);
    }

    public function testCycle(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();

        $taskBuilder = new TaskBuilder();

        $task = $taskBuilder->build($project, $member);

        $childOne = $taskBuilder->build($project, $member);
        $childTwo = $taskBuilder->build($project, $member);

        $childOne->setChildOf($member, new \DateTimeImmutable(), $task);
        $childTwo->setChildOf($member, new \DateTimeImmutable(), $childOne);

        $this->expectExceptionMessage('Cyclomatic children.');
        $task->setChildOf($member, new \DateTimeImmutable(), $childTwo);
    }
}
