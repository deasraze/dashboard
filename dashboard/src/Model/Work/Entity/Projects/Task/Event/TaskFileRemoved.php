<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\File\Id as FileId;
use App\Model\Work\Entity\Projects\Task\File\Info;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskFileRemoved
{
    public Id $taskId;
    public MemberId $actorId;
    public FileId $fileId;
    public Info $info;

    public function __construct(Id $taskId, MemberId $actorId, FileId $fileId, Info $info)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->fileId = $fileId;
        $this->info = $info;
    }
}
