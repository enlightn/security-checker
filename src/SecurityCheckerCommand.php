<?php

namespace Enlightn\SecurityChecker;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SecurityCheckerCommand extends Command
{
    protected static $defaultName = 'security:check';

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('security:check')
            ->setDefinition([
                new InputArgument('lockfile', InputArgument::OPTIONAL, 'The path to the composer.lock file', 'composer.lock'),
            ])
            ->setDescription('Checks for vulnerabilities in your project dependencies')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command looks for security vulnerabilities in the
project dependencies:

<info>php %command.full_name%</info>

You can also pass the path to a <info>composer.lock</info> file as an argument:

<info>php %command.full_name% /path/to/composer.lock</info>

The command displays the result in JSON.
EOF
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = (new SecurityChecker)->check($input->getArgument('lockfile'));
        } catch (Exception $throwable) {
            $output->writeln(json_encode([
                'error' => $throwable->getMessage(),
            ], JSON_PRETTY_PRINT));

            return 1;
        }

        if (count($result) > 0) {
            $output->writeln(json_encode($result, JSON_PRETTY_PRINT));
            return 1;
        }

        return 0;
    }
}
