<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;

class Task
{
    private Id $id;
    private Project $project;
    private Member $author;
    private \DateTimeImmutable $date;
    private ?\DateTimeImmutable $planDate = null;
    private string $name;
    private ?string $content;
    private Type $type;
    private int $progress;
    private int $priority;
    private ?Task $parent = null;

    public function __construct(
        Id $id,
        Project $project,
        Member $author,
        \DateTimeImmutable $date,
        Type $type,
        int $priority,
        string $name,
        ?string $content
    ) {
        $this->id = $id;
        $this->project = $project;
        $this->author = $author;
        $this->date = $date;
        $this->name = $name;
        $this->content = $content;
        $this->type = $type;
        $this->progress = 0;
        $this->priority = $priority;
    }

    public function edit(string $name, ?string $content): void
    {
        $this->name    = $name;
        $this->content = $content;
    }

    public function setChildOf(?Task $parent): void
    {
        if (null !== $parent) {
            $current = $parent;

            do {
                if ($current === $this) {
                    throw new \DomainException('Cyclomatic children.');
                }
            } while (null !== $current = $current->getParent());

        }

        $this->parent = $parent;
    }

    public function plan(?\DateTimeImmutable $date): void
    {
        $this->planDate = $date;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getPlanDate(): ?\DateTimeImmutable
    {
        return $this->planDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }
}
