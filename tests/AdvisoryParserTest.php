<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryParser;
use PHPUnit\Framework\TestCase;

class AdvisoryParserTest extends TestCase
{
    protected AdvisoryParser $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new AdvisoryParser(
            $this->getFixturesDirectory().DIRECTORY_SEPARATOR.'php_security_advisories'
        );
    }

    /**
     * @test
     */
    public function parses_advisories()
    {
        $advisories = $this->parser->getAdvisories();

        $this->assertContains([
            'title' => 'Risk of mass-assignment vulnerabilities',
            'link' => 'https://laravel.com/docs/5.3/upgrade#upgrade-4.1.29',
            'cve' => null,
            'branches' => [
                '4.0.x' => [
                    'time' => 1400581260,
                    'versions' => ['>=4.0.0', '<4.0.99'],
                ],
                '4.1.x' => [
                    'time' => 1400581260,
                    'versions' => ['>=4.1.0', '<4.1.29'],
                ],
            ],
        ], $advisories['laravel/framework']);

        $this->assertContains([
            'title' => 'Password reset phishing vulnerability',
            'link' => 'https://laravel.com/docs/5.4/releases#laravel-5.4.22',
            'cve' => 'CVE-2017-9303',
            'branches' => [
                '5.3.x' => [
                    'time' => null,
                    'versions' => ['>=5.3.0', '<=5.3.31'],
                ],
                '5.4.x' => [
                    'time' => 1494179366,
                    'versions' => ['>=5.4.0', '<5.4.22'],
                ],
            ],
        ], $advisories['illuminate/auth']);
    }

    /**
     * @test
     */
    public function ignores_vulnerabilities_from_allow_list_by_vce()
    {
        $advisories = $this->parser->getAdvisories([
            'CVE-2017-9303'
        ]);

        $this->assertContains([
            'title' => 'Risk of mass-assignment vulnerabilities',
            'link' => 'https://laravel.com/docs/5.3/upgrade#upgrade-4.1.29',
            'cve' => null,
            'branches' => [
                '4.0.x' => [
                    'time' => 1400581260,
                    'versions' => ['>=4.0.0', '<4.0.99'],
                ],
                '4.1.x' => [
                    'time' => 1400581260,
                    'versions' => ['>=4.1.0', '<4.1.29'],
                ],
            ],
        ], $advisories['laravel/framework']);

        $this->assertNotContains([
            'title' => 'Password reset phishing vulnerability',
            'link' => 'https://laravel.com/docs/5.4/releases#laravel-5.4.22',
            'cve' => 'CVE-2017-9303',
            'branches' => [
                '5.3.x' => [
                    'time' => null,
                    'versions' => ['>=5.3.0', '<=5.3.31'],
                ],
                '5.4.x' => [
                    'time' => 1494179366,
                    'versions' => ['>=5.4.0', '<5.4.22'],
                ],
            ],
        ], $advisories['illuminate/auth']);
    }

    /**
     * @test
     */
    public function ignores_vulnerabilities_from_allow_list_by_title()
    {
        $advisories = $this->parser->getAdvisories([
            'Risk of mass-assignment vulnerabilities'
        ]);

        $this->assertNotContains([
            'title' => 'Risk of mass-assignment vulnerabilities',
            'link' => 'https://laravel.com/docs/5.3/upgrade#upgrade-4.1.29',
            'cve' => null,
            'branches' => [
                '4.0.x' => [
                    'time' => 1400581260,
                    'versions' => ['>=4.0.0', '<4.0.99'],
                ],
                '4.1.x' => [
                    'time' => 1400581260,
                    'versions' => ['>=4.1.0', '<4.1.29'],
                ],
            ],
        ], $advisories['laravel/framework']);

        $this->assertContains([
            'title' => 'Password reset phishing vulnerability',
            'link' => 'https://laravel.com/docs/5.4/releases#laravel-5.4.22',
            'cve' => 'CVE-2017-9303',
            'branches' => [
                '5.3.x' => [
                    'time' => null,
                    'versions' => ['>=5.3.0', '<=5.3.31'],
                ],
                '5.4.x' => [
                    'time' => 1494179366,
                    'versions' => ['>=5.4.0', '<5.4.22'],
                ],
            ],
        ], $advisories['illuminate/auth']);
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
