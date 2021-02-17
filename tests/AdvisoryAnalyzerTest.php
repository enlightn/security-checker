<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryAnalyzer;
use Enlightn\SecurityChecker\AdvisoryParser;
use Enlightn\SecurityChecker\Composer;
use PHPUnit\Framework\TestCase;

class AdvisoryAnalyzerTest extends TestCase
{
    /**
     * @test
     */
    public function parses_non_cve_advisories()
    {
        $analyzer = $this->getAnalyzer();

        $this->assertEmpty($analyzer->analyzeDependency('laravel/framework', '8.22.1'));
        $this->assertNotEmpty($analyzer->analyzeDependency('laravel/framework', '8.22.0'));
        $this->assertEquals([[
            'title' => 'Unexpected bindings in QueryBuilder',
            'link' => 'https://blog.laravel.com/security-laravel-62011-7302-8221-released',
            'cve' => null,
        ]], $analyzer->analyzeDependency('laravel/framework', '8.22.0'));
    }

    /**
     * @test
     */
    public function parses_cve_advisories()
    {
        $analyzer = $this->getAnalyzer();

        $this->assertEmpty($analyzer->analyzeDependency('illuminate/auth', '5.5.10'));
        $this->assertNotEmpty($analyzer->analyzeDependency('illuminate/auth', '5.5.9'));
        $this->assertEquals([[
            'title' => 'Timing attack vector for remember me token',
            'link' => 'https://github.com/laravel/framework/pull/21320',
            'cve' => 'CVE-2017-14775',
        ]], $analyzer->analyzeDependency('illuminate/auth', '5.5.9'));
    }

    /**
     * @test
     */
    public function detects_no_vulnerabilities_with_stable_dependencies()
    {
        $analyzer = $this->getAnalyzer();

        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'composer.lock');

        $this->assertEmpty($analyzer->analyzeDependencies($dependencies));
    }

    /**
     * @test
     */
    public function detects_vulnerable_dependencies()
    {
        $analyzer = $this->getAnalyzer();

        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'vulnerable.lock');

        $this->assertEquals([
            'laravel/framework' => [
                'version' => '8.22.0',
                'time' => '2021-01-13T13:37:56+00:00',
                'advisories' => [
                    [
                        'title' => 'Unexpected bindings in QueryBuilder',
                        'link' => 'https://blog.laravel.com/security-laravel-62011-7302-8221-released',
                        'cve' => null,
                    ],
                ],
            ],
        ], $analyzer->analyzeDependencies($dependencies));
    }

    /**
     * @test
     */
    public function detects_vulnerable_dev_package_dependencies()
    {
        $analyzer = $this->getAnalyzer();

        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'branch.lock');

        $this->assertEquals([
            'doctrine/doctrine-module' => [
                'version' => 'dev-master',
                'time' => '2013-05-14T23:57:15+00:00',
                'advisories' => [
                    [
                        'title' => 'Authentication Vulnerability - possible attempt to login via zero-valued password credential',
                        'link' => 'https://github.com/doctrine/DoctrineModule/issues/249',
                        'cve' => null,
                    ],
                ],
            ],
        ], $analyzer->analyzeDependencies($dependencies));
    }

    protected function getAnalyzer()
    {
        $parser = new AdvisoryParser($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'php_security_advisories');

        return new AdvisoryAnalyzer($parser->getAdvisories());
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
