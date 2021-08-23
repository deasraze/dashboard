<?php

declare(strict_types=1);

namespace App\Security\Voter\Work\Projects;

use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectAccess extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const MANAGE_MEMBERS = 'manage_members';

    private AuthorizationCheckerInterface $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (\in_array($attribute, [self::VIEW, self::EDIT, self::MANAGE_MEMBERS], true) && $subject instanceof Project);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserIdentity) {
            return false;
        }

        /* @var Project $project */
        $project = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($project, $user);
            case self::EDIT:
                return $this->canEdit();
            case self::MANAGE_MEMBERS:
                return $this->canManageMembers($project, $user);
        }

        return false;
    }

    private function canView(Project $project, UserIdentity $user): bool
    {
        return $this->canManageProjects() || $project->hasMember(new Id($user->getId()));
    }

    private function canEdit(): bool
    {
        return $this->canManageProjects();
    }

    private function canManageMembers(Project $project, UserIdentity $user): bool
    {
        return
            $this->canManageProjects() ||
            $project->isMemberGranted(new Id($user->getId()), Permission::MANAGE_PROJECT_MEMBERS);
    }

    private function canManageProjects(): bool
    {
        return $this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS');
    }
}
