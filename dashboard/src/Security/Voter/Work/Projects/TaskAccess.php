<?php

declare(strict_types=1);

namespace App\Security\Voter\Work\Projects;

use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskAccess extends Voter
{
    public const VIEW = 'view';
    public const MANAGE = 'manage';
    public const DELETE = 'delete';

    private AuthorizationCheckerInterface $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (\in_array($attribute, [self::VIEW, self::MANAGE], true) && $subject instanceof Task);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserIdentity) {
            return false;
        }

        /* @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case (self::VIEW):
                return $this->canView($task, $user);
            case (self::MANAGE):
                return $this->canManage($task, $user);
            case (self::DELETE):
                return $this->canManageProjects();
        }

        return false;
    }

    private function canView(Task $task, UserIdentity $user): bool
    {
        return
            $this->canManageProjects() ||
            $task->getProject()->isMemberGranted(new Id($user->getId()), Permission::VIEW_TASKS);
    }

    private function canManage(Task $task, UserIdentity $user): bool
    {
        return
            $this->canManageProjects() ||
            $task->getProject()->isMemberGranted(new Id($user->getId()), Permission::MANAGE_TASKS);
    }

    private function canManageProjects(): bool
    {
        return $this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS');
    }
}
