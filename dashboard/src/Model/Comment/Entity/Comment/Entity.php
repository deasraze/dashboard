<?php

declare(strict_types=1);

namespace App\Model\Comment\Entity\Comment;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Entity
{
    /**
     * @ORM\Column(type="string")
     */
    private string $type;
    /**
     * @ORM\Column(type="string", length=36)
     */
    private string $id;

    public function __construct(string $type, string $id)
    {
        Assert::notEmpty($type);
        Assert::notEmpty($id);

        $this->type = $type;
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
