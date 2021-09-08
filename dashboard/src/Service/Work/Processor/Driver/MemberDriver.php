<?php

declare(strict_types=1);

namespace App\Service\Work\Processor\Driver;

use App\ReadModel\Work\Members\Member\MemberFetcher;
use Twig\Environment;

class MemberDriver implements Driver
{
    private const PATTERN = '/@[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i';

    private MemberFetcher $members;
    private Environment $twig;

    public function __construct(MemberFetcher $members, Environment $twig)
    {
        $this->members = $members;
        $this->twig = $twig;
    }

    public function process(string $text): string
    {
        return preg_replace_callback(self::PATTERN, function (array $matches) {
            $id = \ltrim($matches[0], '@');

            if (null === $member = $this->members->find($id)) {
                return $matches[0];
            }

            return $this->twig->render('processor/work/member.html.twig', compact('member'));
        }, $text);
    }
}
