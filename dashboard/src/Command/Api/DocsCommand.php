<?php

declare(strict_types=1);

namespace App\Command\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class DocsCommand extends Command
{
    protected static $defaultName = 'api:docs';
    protected static $defaultDescription = 'Generate OpenAPI docs.';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $swagger = 'vendor/bin/openapi';
        $source = 'src/Controller';
        $target = 'public/docs/openapi.json';

        $process = new Process([PHP_BINARY, $swagger, $source, '--output', $target]);
        $process->run(static fn ($type, $buffer) => $output->write($buffer));

        $io->success('Done!');

        return 1;
    }
}
