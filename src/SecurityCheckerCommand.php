<?php

namespace Enlightn\SecurityChecker;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'The output format', 'ansi'),
            ])
            ->setDescription('Checks for vulnerabilities in your project dependencies')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command looks for security vulnerabilities in the
project dependencies:

<info>php %command.full_name%</info>

You can also pass the path to a <info>composer.lock</info> file as an argument:

<info>php %command.full_name% /path/to/composer.lock</info>

By default, the command displays the result in ansi, but you can also
configure it to output JSON instead by using the <info>--format</info> option:

<info>php %command.full_name% /path/to/composer.lock --format=json</info>
EOF
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $input->getOption('format') == 'ansi' ? new AnsiFormatter : new JsonFormatter;

        try {
            $result = (new SecurityChecker)->check($input->getArgument('lockfile'));

            $formatter->displayResult($output, $result);
        } catch (Exception $throwable) {
            $formatter->displayError($output, $throwable);

            return 1;
        }

        if (count($result) > 0) {
            return 1;
        }

        return 0;
    }
}
