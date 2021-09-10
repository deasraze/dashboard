<?php

declare(strict_types=1);

namespace App\Model\Comment\Entity\Comment;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment_comments", indexes={
 *     @ORM\Index(columns={"date"}),
 *     @ORM\Index(columns={"entity_type", "entity_id"})
 * })
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="comment_comment_id")
     */
    private Id $id;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Column(type="comment_comment_author_id")
     */
    private AuthorId $authorId;
    /**
     * @ORM\Embedded(class="Entity")
     */
    private Entity $entity;
    /**
     * @ORM\Column(type="text")
     */
    private string $text;
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $updateDate;
    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private int $version;

    public function __construct(Id $id, AuthorId $authorId, Entity $entity, \DateTimeImmutable $date, string $text)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->entity = $entity;
        $this->date = $date;
        $this->text = $text;
    }

    public function edit(\DateTimeImmutable $date, string $text): void
    {
        $this->updateDate = $date;
        $this->text = $text;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getAuthorId(): AuthorId
    {
        return $this->authorId;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
