<?php

declare(strict_types=1);

namespace App\DataFixtures\Work\Projects;

use App\DataFixtures\Work\Members\MemberFixture;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_FIRST = 'work_project_first';
    public const REFERENCE_SECOND = 'work_project_second';

    public function load(ObjectManager $manager): void
    {
        /**
         * @var Member $admin
         * @var Member $user
         */
        $admin = $this->getReference(MemberFixture::REFERENCE_ADMIN);
        $user = $this->getReference(MemberFixture::REFERENCE_USER);

        /**
         * @var Role $manage
         * @var Role $guest
         */
        $manage = $this->getReference(RoleFixture::REFERENCE_MANAGER);
        $guest = $this->getReference(RoleFixture::REFERENCE_GUEST);

        $active = $this->createProject('First project', 1);

        $active->addDepartment($development = DepartmentId::next(), 'Development');
        $active->addDepartment($marketing = DepartmentId::next(), 'Marketing');
        $active->addMember($admin, [$development], [$manage]);
        $active->addMember($user, [$marketing], [$guest]);

        $manager->persist($active);
        $this->addReference(self::REFERENCE_FIRST, $active);

        $active = $this->createProject('Second project', 2);

        $active->addDepartment($development = DepartmentId::next(), 'Development');
        $active->addMember($admin, [$development], [$guest]);

        $manager->persist($active);
        $this->addReference(self::REFERENCE_SECOND, $active);

        $archived = $this->createArchivedProject('Third project', 3);

        $manager->persist($archived);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MemberFixture::class,
            RoleFixture::class,
        ];
    }

    private function createArchivedProject(string $name, int $sort): Project
    {
        $project = $this->createProject($name, $sort);

        $project->archive();

        return $project;
    }

    private function createProject(string $name, int $sort): Project
    {
        return new Project(
            Id::next(),
            $name,
            $sort
        );
    }
}
