<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\UseCase\SignUp\Confirm;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfirmCommand extends Command
{
    protected static $defaultName = 'user:confirm';
    protected static $defaultDescription = 'Confirms signed up user';

    private UserFetcher $users;
    private Confirm\Manual\Handler $handler;

    public function __construct(UserFetcher $users, Confirm\Manual\Handler $handler)
    {
        parent::__construct();

        $this->users = $users;
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'm', InputOption::VALUE_OPTIONAL, 'User email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        if (null === $email = $input->getOption('email')) {
            $email = $helper->ask($input, $output, new Question('Email: '));
        }

        if (!$user = $this->users->findByEmail($email)) {
            throw new LogicException('User is not found');
        }

        $command = new Confirm\Manual\Command($user->id);

        $this->handler->handle($command);

        $io->success('User is successfully confirmed!');

        return Command::SUCCESS;
    }
}
