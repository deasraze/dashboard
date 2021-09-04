<?php

declare(strict_types=1);

namespace App\Event\Dispatcher\Message;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Handler implements MessageHandlerInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Message $message)
    {
        $this->dispatcher->dispatch($message->getEvent());
    }
}
