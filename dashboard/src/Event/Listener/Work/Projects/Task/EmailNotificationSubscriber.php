<?php

declare(strict_types=1);

namespace App\Event\Listener\Work\Projects\Task;

use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Event\TaskExecutorAssigned;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailNotificationSubscriber implements EventSubscriberInterface
{
    private TaskRepository $tasks;
    private MemberRepository $members;
    private MailerInterface $mailer;

    public function __construct(TaskRepository $tasks, MemberRepository $members, MailerInterface $mailer)
    {
        $this->tasks = $tasks;
        $this->members = $members;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TaskExecutorAssigned::class => [
                ['onTaskExecutorAssignedExecutor', 10],
                ['onTaskExecutorAssignedAuthor'],
            ],
        ];
    }

    public function onTaskExecutorAssignedExecutor(TaskExecutorAssigned $event): void
    {
        if ($event->actorId === $event->executorId) {
            return;
        }

        $task = $this->tasks->get($event->taskId);
        $executor = $this->members->get($event->executorId);
        $author = $task->getAuthor();

        if ($author === $executor) {
            return;
        }

        $message = (new TemplatedEmail())
            ->to(
                new Address(
                    $executor->getEmail()->getValue(),
                    $executor->getName()->getFull()
                )
            )
            ->subject('You have been assigned as the task executor')
            ->htmlTemplate('mail/work/projects/task/executor-assigned-executor.html.twig')
            ->context([
                'task' => $task,
                'executor' => $executor,
            ]);

        $this->mailer->send($message);
    }

    public function onTaskExecutorAssignedAuthor(TaskExecutorAssigned $event): void
    {
        $task = $this->tasks->get($event->taskId);
        $executor = $this->members->get($event->executorId);
        $author = $task->getAuthor();

        if ($author === $executor) {
            return;
        }

        $message = (new TemplatedEmail())
            ->to(
                new Address(
                    $author->getEmail()->getValue(),
                    $author->getName()->getFull()
                )
            )
            ->subject('Executor has been assigned to your task')
            ->htmlTemplate('mail/work/projects/task/executor-assigned-author.html.twig')
            ->context([
                'task' => $task,
                'author' => $author,
                'executor' => $executor,
            ]);

        $this->mailer->send($message);
    }
}
