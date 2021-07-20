<?php

namespace App\Command\User;

use App\Model\User\Entity\User\Role as RoleValue;
use App\ReadModel\User\UserFetcher;
use App\Model\User\UseCase\Role;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleCommand extends Command
{
    protected static $defaultName = 'user:role';
    protected static $defaultDescription = 'Changes user role';

    private UserFetcher $users;
    private ValidatorInterface $validator;
    private Role\Handler $handler;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, Role\Handler $handler)
    {
        parent::__construct();

        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'm', InputOption::VALUE_OPTIONAL, 'User email')
        ;
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

        $roles = [RoleValue::USER, RoleValue::ADMIN];

        $command = new Role\Command($user->id);
        $command->role = (string) $helper->ask($input, $output, new ChoiceQuestion('Role: ', $roles, 0));

        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $io->error($violation->getPropertyPath() . ':' . $violation->getMessage());
            }

            return Command::INVALID;
        }

        $this->handler->handle($command);

        $io->success('User role is successfully changed!');

        return Command::SUCCESS;
    }
}
