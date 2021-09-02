<?php

declare(strict_types=1);

namespace App\Model\Comment\UseCase\Comment\Create;

use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Flusher;

class Handler
{
    private CommentRepository $comments;
    private Flusher $flusher;

    public function __construct(CommentRepository $comments, Flusher $flusher)
    {
        $this->comments = $comments;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $comment = new Comment(
            Id::next(),
            new AuthorId($command->author),
            new Entity(
                $command->entityType,
                $command->entityId,
            ),
            new \DateTimeImmutable(),
            $command->text
        );

        $this->comments->add($comment);

        $this->flusher->flush();
    }
}
