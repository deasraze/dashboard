<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $project;
    /**
     * @Assert\NotBlank()
     */
    public string $id;

    public function __construct(string $project, string $id)
    {
        $this->project = $project;
        $this->id = $id;
    }
}
