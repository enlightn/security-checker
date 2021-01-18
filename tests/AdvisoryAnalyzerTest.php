<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryAnalyzer;
use Enlightn\SecurityChecker\AdvisoryParser;
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

    protected function getAnalyzer(): AdvisoryAnalyzer
    {
        $parser = new AdvisoryParser($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'php_security_advisories');

        return new AdvisoryAnalyzer($parser->getAdvisories());
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
