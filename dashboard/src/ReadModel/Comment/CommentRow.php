<?php

declare(strict_types=1);

namespace App\ReadModel\Comment;

class CommentRow
{
    public string $id;
    public string $date;
    public string $author_id;
    public string $author_name;
    public string $author_email;
    public string $text;
}
