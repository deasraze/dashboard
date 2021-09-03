<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Change;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_task_changes")
 */
class Change
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="work_projects_task_change_id")
     */
    private Id $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Task\Task", inversedBy="changes")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Task $task;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Members\Member\Member")
     * @ORM\JoinColumn(name="actor_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Member $actor;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Embedded(class="Set")
     */
    private Set $set;

    public function __construct(Id $id, Task $task, Member $actor, \DateTimeImmutable $date, Set $set)
    {
        $this->id = $id;
        $this->task = $task;
        $this->actor = $actor;
        $this->date = $date;
        $this->set = $set;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getActor(): Member
    {
        return $this->actor;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getSet(): Set
    {
        return $this->set;
    }
}
