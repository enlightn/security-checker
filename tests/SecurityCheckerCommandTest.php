<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\SecurityCheckerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SecurityCheckerCommandTest extends TestCase
{
    /**
     * @test
     */
    public function displays_vulnerabilities_in_ansi()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertTrue(strpos($commandTester->getDisplay(), 'Unexpected bindings in QueryBuilder') !== false);
    }

    /**
     * @test
     */
    public function displays_vulnerabilities_in_json()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
            '--format' => 'json',
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertTrue(strpos($commandTester->getDisplay(), 'Unexpected bindings in QueryBuilder') !== false);
        $this->assertJson($commandTester->getDisplay());
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
