<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     */
    public string $member;
    /**
     * @var File[]
     */
    public array $files;

    public function __construct(int $id, string $member)
    {
        $this->id = $id;
        $this->member = $member;
    }
}
