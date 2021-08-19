<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project\Department;

use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_project_departments")
 */
class Department
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="work_projects_project_department_id")
     */
    private Id $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Project\Project", inversedBy="departments")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private Project $project;
    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    public function __construct(Id $id, Project $project, string $name)
    {
        $this->id = $id;
        $this->project = $project;
        $this->name = $name;
    }

    public function edit(string $name): void
    {
        $this->name = $name;
    }

    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
