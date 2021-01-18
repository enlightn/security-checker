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

        $this->assertSame($dependencies['laravel/framework'], '8.22.1');
        $this->assertSame($dependencies['voku/portable-ascii'], '1.5.6');
        $this->assertSame($dependencies['orchestra/testbench'], '6.8.0');
        $this->assertSame($dependencies['phpunit/phpunit'], '9.5.1');
    }

    /**
     * @test
     */
    public function can_exclude_dev_dependencies_from_lock_file()
    {
        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'composer.lock', true);

        $this->assertSame($dependencies['laravel/framework'], '8.22.1');
        $this->assertSame($dependencies['voku/portable-ascii'], '1.5.6');
        $this->assertArrayNotHasKey('orchestra/testbench', $dependencies);
        $this->assertArrayNotHasKey('phpunit/phpunit', $dependencies);
    }

    /**
     * @test
     */
    public function fetches_all_dependencies_from_installed_json_file()
    {
        $dependencies = (new Composer)->getDependencies($this->getFixturesDirectory().DIRECTORY_SEPARATOR.'installed.json');

        $this->assertSame($dependencies['laravel/framework'], '8.22.1');
        $this->assertSame($dependencies['voku/portable-ascii'], '1.5.6');
        $this->assertSame($dependencies['orchestra/testbench'], '6.8.0');
        $this->assertSame($dependencies['phpunit/phpunit'], '9.5.1');
    }

    protected function getFixturesDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures';
    }
}
