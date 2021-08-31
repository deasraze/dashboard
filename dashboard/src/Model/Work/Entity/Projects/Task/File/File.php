<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\File;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="work_projects_task_files", indexes={
 *     @ORM\Index(columns={"date"})
 * )}
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="work_projects_task_file_id")
     */
    private Id $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Task\Task", inversedBy="files")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false)
     */
    private Task $task;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Members\Member\Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false)
     */
    private Member $member;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Embedded(class="Info")
     */
    private Info $info;

    public function __construct(Id $id, Task $task, Member $member, \DateTimeImmutable $date, Info $info)
    {
        $this->id = $id;
        $this->task = $task;
        $this->member = $member;
        $this->date = $date;
        $this->info = $info;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }
}
