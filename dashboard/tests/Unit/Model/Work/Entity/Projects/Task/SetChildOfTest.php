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

        $task->setChildOf($parent);

        self::assertEquals($parent, $task->getParent());
    }

    public function testEmpty(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();

        $taskBuilder = new TaskBuilder();

        $task = $taskBuilder->build($project, $member);
        $parent = $taskBuilder->build($project, $member);

        $task->setChildOf($parent);
        $task->setChildOf(null);

        self::assertNull($task->getParent());
    }

    public function testSelf(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $this->expectExceptionMessage('Cyclomatic children.');
        $task->setChildOf($task);
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

        $childOne->setChildOf($task);
        $childTwo->setChildOf($childOne);

        $this->expectExceptionMessage('Cyclomatic children.');
        $task->setChildOf($childTwo);
    }
}
