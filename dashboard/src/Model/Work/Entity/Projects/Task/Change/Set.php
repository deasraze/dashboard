<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Change;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Task\File\Id as FileId;
use App\Model\Work\Entity\Projects\Task\Id as TaskId;
use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Set
{
    /**
     * @ORM\Column(type="work_projects_project_id", nullable=true)
     */
    private ?ProjectId $projectId = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;
    /**
     * @ORM\Column(type="work_projects_task_file_id", nullable=true)
     */
    private ?FileId $fileId = null;
    /**
     * @ORM\Column(type="work_projects_task_file_id", nullable=true)
     */
    private ?FileId $removedFileId = null;
    /**
     * @ORM\Column(type="work_projects_task_type", length=16, nullable=true)
     */
    private ?Type $type = null;
    /**
     * @ORM\Column(type="work_projects_task_status", length=16, nullable=true)
     */
    private ?Status $status = null;
    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $progress = null;
    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $priority = null;
    /**
     * @ORM\Column(type="work_projects_task_id", nullable=true)
     */
    private ?TaskId $parentId = null;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $removedParent = null;
    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $plan = null;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $removedPlan = null;
    /**
     * @ORM\Column(type="work_members_member_id", nullable=true)
     */
    private ?MemberId $executorId = null;
    /**
     * @ORM\Column(type="work_members_member_id", nullable=true)
     */
    private ?MemberId $revokedExecutorId = null;

    private function __construct()
    {
    }

    public static function forNewTask(ProjectId $project, Type $type, int $priority, string $name, ?string $content): self
    {
        $set = new self();

        $set->projectId = $project;
        $set->type = $type;
        $set->priority = $priority;
        $set->name = $name;
        $set->content = $content;

        return $set;
    }

    public static function fromProject(ProjectId $project): self
    {
        $set = new self();

        $set->projectId = $project;

        return $set;
    }

    public static function fromName(string $name): self
    {
        $set = new self();

        $set->name = $name;

        return $set;
    }

    public static function fromContent(string $content): self
    {
        $set = new self();

        $set->content = $content;

        return $set;
    }

    public static function fromFile(FileId $file): self
    {
        $set = new self();

        $set->fileId = $file;

        return $set;
    }

    public static function fromRemovedFile(FileId $file): self
    {
        $set = new self();

        $set->removedFileId = $file;

        return $set;
    }

    public static function fromType(Type $type): self
    {
        $set = new self();

        $set->type = $type;

        return $set;
    }

    public static function fromStatus(Status $status): self
    {
        $set = new self();

        $set->status = $status;

        return $set;
    }

    public static function fromProgress(int $progress): self
    {
        $set = new self();

        $set->progress = $progress;

        return $set;
    }

    public static function fromPriority(int $priority): self
    {
        $set = new self();

        $set->priority = $priority;

        return $set;
    }

    public static function fromParent(TaskId $parent): self
    {
        $set = new self();

        $set->parentId = $parent;

        return $set;
    }

    public static function fromRemovedParent(): self
    {
        $set = new self();

        $set->removedParent = true;

        return $set;
    }

    public static function fromPlan(\DateTimeImmutable $plan): self
    {
        $set = new self();

        $set->plan = $plan;

        return $set;
    }

    public static function fromRemovedPlan(): self
    {
        $set = new self();

        $set->removedPlan = true;

        return $set;
    }

    public static function fromExecutor(MemberId $executor): self
    {
        $set = new self();

        $set->executorId = $executor;

        return $set;
    }

    public static function fromRevokedExecutor(MemberId $executor): self
    {
        $set = new self();

        $set->revokedExecutorId = $executor;

        return $set;
    }
}
