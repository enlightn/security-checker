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
    public function scan_issues_in_dev_packages()
    {
      $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable-dev.lock';

      $command = new SecurityCheckerCommand;
      $commandTester = new CommandTester($command);

      $commandTester->execute([
        'lockfile' => $lockFile,
        '--no-dev' => false // Same value if omitted
      ]);

      self::assertEquals(
        1,
        $commandTester->getStatusCode(),
        "Expected dev package in 'vulnerable-dev.lock' to report a vulnerability"
      );

      self::assertTrue(
        strpos(
          $commandTester->getDisplay(),
          'RCE vulnerability in phpunit'
        ) !== false,
        "Expected dev package in 'vulnerable-dev.lock' to report a vulnerability"
      );
    }

    /**
     * @test
     */
    public function ignore_issues_in_dev_packages()
    {
      $lockFile = $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable-dev.lock';

      $command = new SecurityCheckerCommand;
      $commandTester = new CommandTester($command);

      $commandTester->execute([
        'lockfile' => $lockFile,
        '--no-dev' => true
      ]);

      self::assertEquals(
        0,
        $commandTester->getStatusCode(),
        'Scan has detected a security issue despite excluding dev packages'
      );

      self::assertTrue(
        strpos(
          $commandTester->getDisplay(),
          '[OK] 0 packages have known vulnerabilities'
        ) !== false,
        'Scan has detected a security issue despite excluding dev packages'
      );
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
