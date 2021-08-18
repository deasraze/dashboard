<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Edit;

use App\Model\Work\Entity\Projects\Project\Project;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $id;
    /**
     * @Assert\NotBlank()
     */
    public string $name;
    /**
     * @Assert\NotBlank()
     */
    public int $sort;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromProject(Project $project): self
    {
        $command = new self($project->getId()->getValue());

        $command->name = $project->getName();
        $command->sort = $project->getSort();

        return $command;
    }
}
