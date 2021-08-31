<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

use Symfony\Component\Validator\Constraints as Assert;

class File
{
    /**
     * @Assert\NotBlank()
     */
    public string $path;
    /**
     * @Assert\NotBlank()
     */
    public string $name;
    /**
     * @Assert\NotBlank()
     */
    public int $size;

    public function __construct(string $path, string $name, int $size)
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
    }
}
