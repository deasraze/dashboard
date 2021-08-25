<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Edit;

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
    public string $name;
    public ?string $content;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromTask(Task $task): self
    {
        $command = new self($task->getId()->getValue());

        $command->name = $task->getName();
        $command->content = $task->getContent();

        return $command;
    }
}
