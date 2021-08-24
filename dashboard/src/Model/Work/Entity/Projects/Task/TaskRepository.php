<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TaskRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Task::class);
    }

    public function get(Id $id): Task
    {
        if (!$task = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Task is not found.');
        }

        return $task;
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
    }
}
