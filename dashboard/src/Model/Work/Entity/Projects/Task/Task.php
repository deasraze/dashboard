<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_tasks", indexes={
 *     @ORM\Index(columns={"date"})
 * })
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="work_projects_task_id")
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\SequenceGenerator(sequenceName="work_projects_tasks_seq", initialValue=1)
     */
    private Id $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Project\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private Project $project;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Members\Member\Member")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private Member $author;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $planDate = null;
    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $startDate = null;
    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $endDate = null;
    /**
     * @ORM\Column(type="string")
     */
    private string $name;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content;
    /**
     * @ORM\Column(type="work_projects_task_type", length=16)
     */
    private Type $type;
    /**
     * @ORM\Column(type="smallint")
     */
    private int $progress;
    /**
     * @ORM\Column(type="smallint")
     */
    private int $priority;
    /**
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Task $parent = null;
    /**
     * @ORM\Column(type="work_projects_task_status", length=16)
     */
    private Status $status;
    /**
     * @var ArrayCollection|Member[]
     * @ORM\ManyToMany(targetEntity="App\Model\Work\Entity\Members\Member\Member")
     * @ORM\JoinTable(name="work_projects_tasks_executors",
     *     joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name.first" = "ASC"})
     */
    private $executors;

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
        $this->status = Status::new();
        $this->executors = new ArrayCollection();
    }

    public function start(\DateTimeImmutable $date): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('Task is already started.');
        }

        if (0 === $this->executors->count()) {
            throw new \DomainException('Task does not contain executors.');
        }

        $this->changeStatus(Status::working(), $date);
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

    public function move(Project $project): void
    {
        if ($this->project === $project) {
            throw new \DomainException('Project is already same.');
        }

        $this->project = $project;
    }

    public function changeType(Type $type): void
    {
        if ($this->type->isEqual($type)) {
            throw new \DomainException('Type is already same.');
        }

        $this->type = $type;
    }

    public function changeProgress(int $progress): void
    {
        Assert::range($progress, 0, 100);

        if ($this->progress === $progress) {
            throw new \DomainException('Progress is already same.');
        }

        $this->progress = $progress;
    }

    public function changePriority(int $priority): void
    {
        Assert::range($priority, 1, 4);

        if ($this->priority === $priority) {
            throw new \DomainException('Priority is already same.');
        }

        $this->priority = $priority;
    }

    public function changeStatus(Status $status, \DateTimeImmutable $date): void
    {
        if ($this->status->isEqual($status)) {
            throw new \DomainException('Status is already same.');
        }

        $this->status = $status;

        if (!$status->isNew() && null === $this->startDate) {
            $this->startDate = $date;
        }

        if ($status->isDone()) {
            if ($this->progress !== 100) {
                $this->changeProgress(100);
            }

            $this->endDate = $date;
        } else {
            $this->endDate = null;
        }
    }

    public function assignExecutor(Member $executor): void
    {
        if ($this->executors->contains($executor)) {
            throw new \DomainException('Executor is already assigned.');
        }

        $this->executors->add($executor);
    }

    public function revokeExecutor(MemberId $id): void
    {
        foreach ($this->executors as $executor) {
            if ($executor->getId()->isEqual($id)) {
                $this->executors->removeElement($executor);

                return;
            }
        }

        throw new \DomainException('Executor is not assigned.');
    }

    public function hasExecutor(MemberId $id): bool
    {
        foreach ($this->executors as $executor) {
            if ($executor->getId()->isEqual($id)) {
                return true;
            }
        }

        return false;
    }

    public function isNew(): bool
    {
        return $this->status->isNew();
    }

    public function isWorking(): bool
    {
        return $this->status->isWorking();
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

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Member[]
     */
    public function getExecutors(): array
    {
        return $this->executors->toArray();
    }
}