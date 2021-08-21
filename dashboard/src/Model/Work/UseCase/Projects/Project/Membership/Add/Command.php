<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

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
    public string $member;
    /**
     * @Assert\NotBlank()
     */
    public array $departments;
    /**
     * @Assert\NotBlank()
     */
    public array $roles;

    public function __construct(string $project)
    {
        $this->project = $project;
    }
}
