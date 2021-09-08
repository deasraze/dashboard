<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Knp\Component\Pager\Pagination\PaginationInterface;

class PaginationNormalizer
{
    public static function normalize(PaginationInterface $pagination): array
    {
        return [
            'total' => $pagination->count(),
            'count' => $pagination->getTotalItemCount(),
            'per_page' => $pagination->getItemNumberPerPage(),
            'page' => $pagination->getCurrentPageNumber(),
            'pages' => \ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
        ];
    }
}
