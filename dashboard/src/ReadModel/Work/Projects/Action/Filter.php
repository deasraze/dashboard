<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Action;

class Filter
{
    public ?string $project = null;
    public ?string $member = null;

    private function __construct(?string $project)
    {
        $this->project = $project;
    }

    public static function all(): self
    {
        return new self(null);
    }

    public static function forProject(string $project): self
    {
        return new self($project);
    }

    public function forMember(string $member): self
    {
        $clone = clone $this;

        $clone->member = $member;

        return $clone;
    }
}
