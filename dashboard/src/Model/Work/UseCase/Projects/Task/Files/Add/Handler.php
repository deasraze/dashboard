<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\File\Id as FileId;
use App\Model\Work\Entity\Projects\Task\File\Info;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
    private TaskRepository $tasks;
    private MemberRepository $members;
    private Flusher $flusher;

    public function __construct(TaskRepository $tasks, MemberRepository $members, Flusher $flusher)
    {
        $this->tasks = $tasks;
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $task = $this->tasks->get(new Id($command->id));
        $actor = $this->members->get(new MemberId($command->actor));

        foreach ($command->files as $file) {
            $task->addFile(
                $actor,
                new \DateTimeImmutable(),
                FileId::next(),
                new Info(
                    $file->path,
                    $file->name,
                    $file->size
                )
            );
        }

        $this->flusher->flush();
    }
}
