<?php

declare(strict_types=1);

namespace App\Security\Voter\Comment;

use App\Model\Comment\Entity\Comment\Comment;
use App\ReadModel\Comment\CommentRow;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentAccess extends Voter
{
    public const MANAGE = 'manage';

    private AuthorizationCheckerInterface $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return self::MANAGE == $attribute && ($subject instanceof Comment || $subject instanceof CommentRow);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserIdentity) {
            return false;
        }

        $own = '';

        if ($subject instanceof Comment) {
            $own = $subject->getAuthorId()->getValue();
        }

        if ($subject instanceof CommentRow) {
            $own = $subject->author_id;
        }

        switch ($attribute) {
            case self::MANAGE:
                return $this->canManage($user, $own);
        }

        return false;
    }

    private function canManage(UserIdentity $user, string $own): bool
    {
        return $this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS') || $own === $user->getId();
    }
}
