<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryParser;
use PHPUnit\Framework\TestCase;

class AdvisoryParserTest extends TestCase
{
    /**
     * @test
     */
    public function parses_advisories()
    {
        $parser = new AdvisoryParser($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'php_security_advisories');

        $advisories = $parser->getAdvisories();

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

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
