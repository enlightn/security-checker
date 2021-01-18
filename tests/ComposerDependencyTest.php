<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\Composer;
use PHPUnit\Framework\TestCase;

class ComposerDependencyTest extends TestCase
{
    /**
     * @test
     */
    public function fetches_all_dependencies_from_lock_file()
    {
        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'composer.lock');

        $this->assertSame($dependencies['laravel/framework'], ['version' => '8.22.1', 'time' => '2021-01-13T13:37:56+00:00']);
        $this->assertSame($dependencies['voku/portable-ascii'], ['version' => '1.5.6', 'time' => '2020-11-12T00:07:28+00:00']);
        $this->assertSame($dependencies['orchestra/testbench'], ['version' => '6.8.0', 'time' => '2021-01-17T09:03:09+00:00']);
        $this->assertSame($dependencies['phpunit/phpunit'], ['version' => '9.5.1', 'time' => '2021-01-17T07:42:25+00:00']);
    }

    /**
     * @test
     */
    public function can_exclude_dev_dependencies_from_lock_file()
    {
        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'composer.lock', true);

        $this->assertSame($dependencies['laravel/framework'], ['version' => '8.22.1', 'time' => '2021-01-13T13:37:56+00:00']);
        $this->assertSame($dependencies['voku/portable-ascii'], ['version' => '1.5.6', 'time' => '2020-11-12T00:07:28+00:00']);
        $this->assertArrayNotHasKey('orchestra/testbench', $dependencies);
        $this->assertArrayNotHasKey('phpunit/phpunit', $dependencies);
    }

    /**
     * @test
     */
    public function fetches_all_dependencies_from_installed_json_file()
    {
        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'installed.json');

        $this->assertSame($dependencies['laravel/framework'], ['version' => '8.22.1', 'time' => '2021-01-13T13:37:56+00:00']);
        $this->assertSame($dependencies['voku/portable-ascii'], ['version' => '1.5.6', 'time' => '2020-11-12T00:07:28+00:00']);
        $this->assertSame($dependencies['orchestra/testbench'], ['version' => '6.8.0', 'time' => '2021-01-17T09:03:09+00:00']);
        $this->assertSame($dependencies['phpunit/phpunit'], ['version' => '9.5.1', 'time' => '2021-01-17T07:42:25+00:00']);
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
