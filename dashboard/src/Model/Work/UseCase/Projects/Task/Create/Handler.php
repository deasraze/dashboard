<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Projects\Task\Type;

class Handler
{
    private ProjectRepository $projects;
    private MemberRepository $members;
    private TaskRepository $tasks;
    private Flusher $flusher;

    public function __construct(ProjectRepository $projects, MemberRepository $members, TaskRepository $tasks, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->members = $members;
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get(new ProjectId($command->project));
        $member = $this->members->get(new MemberId($command->member));

        $task = new Task(
            $this->tasks->nextId(),
            $project,
            $member,
            new \DateTimeImmutable(),
            new Type($command->type),
            $command->priority,
            $command->name,
            $command->content
        );

        if (null !== $command->parent) {
            $parent = $this->tasks->get(new Id($command->parent));
            $task->setChildOf($parent);
        }

        if (null !== $command->plan) {
            $task->plan($command->plan);
        }

        $this->tasks->add($task);

        $this->flusher->flush();
    }
}
