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

    /**
     * @test
     */
    public function scans_issues_in_dev_packages()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable-dev.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertTrue(strpos($commandTester->getDisplay(), 'RCE vulnerability in phpunit') !== false);
    }

    /**
     * @test
     */
    public function can_ignore_issues_in_dev_packages()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable-dev.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
        'lockfile' => $lockFile,
        '--no-dev' => true,
      ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertTrue(strpos($commandTester->getDisplay(), '[OK] 0 packages have known vulnerabilities') !== false);
    }

    /**
     * @test
     */
    public function can_allow_vulnerabilities_by_cve()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable-dev.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
            '--allow-list' => [
                'CVE-2017-9841'
            ]
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertTrue(strpos($commandTester->getDisplay(), '[OK] 0 packages have known vulnerabilities') !== false);
    }

    /**
     * @test
     */
    public function can_allow_vulnerabilities_by_title()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable-dev.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
            '--allow-list' => [
                'RCE vulnerability in phpunit'
            ]
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertTrue(strpos($commandTester->getDisplay(), '[OK] 0 packages have known vulnerabilities') !== false);
    }

    /**
     * @test
     */
    public function can_chose_unzip_with_unzip_command()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'installed.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
            '--use-ext' => 'system-unzip'
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    /**
     * @test
     */
    public function can_chose_unzip_with_php_zip_extension()
    {
        $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'installed.lock';

        $command = new SecurityCheckerCommand;
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'lockfile' => $lockFile,
            '--use-ext' => 'zip-extension'
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
