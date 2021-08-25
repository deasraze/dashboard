<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\EntityNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TaskRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;
    private Connection $connection;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Task::class);
        $this->connection = $em->getConnection();
    }

    public function get(Id $id): Task
    {
        if (!$task = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Task is not found.');
        }

        return $task;
    }

    /**
     * @return Task[]
     */
    public function allByParent(Id $id): array
    {
        return $this->repo->findBy(['parent' => $id->getValue()]);
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
    }

    public function nextId(): Id
    {
        return new Id((int) $this->connection->executeQuery('SELECT nextval(\'work_projects_tasks_seq\')')->fetchOne());
    }
}
