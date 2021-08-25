<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Move;

use App\Model\Work\Entity\Projects\Task\Task;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     */
    public string $project;
    /**
     * @Assert\Type("bool")
     */
    public bool $withChildren;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromTask(Task $task): self
    {
        $command = new self($task->getId()->getValue());

        $command->project = $task->getProject()->getId()->getValue();

        return $command;
    }
}
