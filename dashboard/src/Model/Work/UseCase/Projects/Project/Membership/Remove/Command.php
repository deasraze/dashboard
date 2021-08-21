<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

class Command
{
    public string $project;
    public string $member;

    public function __construct(string $project, string $member)
    {
        $this->project = $project;
        $this->member = $member;
    }
}
