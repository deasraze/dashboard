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

        $parent = $command->parent ? $this->tasks->get(new Id($command->parent)) : null;

        $date = new \DateTimeImmutable();

        $tasks = [];

        foreach ($command->names as $name) {
            $task = new Task(
                $this->tasks->nextId(),
                $project,
                $member,
                $date,
                new Type($command->type),
                $command->priority,
                $name->name,
                $command->content
            );

            if (null !== $parent) {
                $task->setChildOf($member, $date, $parent);
            }

            if (null !== $command->plan) {
                $task->plan($member, $date, $command->plan);
            }

            $date = $date->modify('+2 sec');

            $this->tasks->add($task);

            $tasks[] = $task;
        }

        $this->flusher->flush(...$tasks);
    }
}
