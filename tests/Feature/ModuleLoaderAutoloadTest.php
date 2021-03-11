<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature;

use SebastiaanLuca\Module\Tests\TestCase;

class ModuleLoaderAutoloadTest extends TestCase
{
    /**
     * @test
     */
    public function it scans and autoloads all modules(): void
    {
        $this->getAutoloader()->shouldReceive('addPsr4')->with('Another\\', 'modules/Another/src/', true)->once();
        $this->getAutoloader()->shouldReceive('addPsr4')->with('Missing\\', 'modules/Missing/src/', true)->once();
        $this->getAutoloader()->shouldReceive('addPsr4')->with('MyModule\\', 'modules/MyModule/src/', true)->once();

        $this->getAutoloader()->shouldReceive('add');

        $this->getModuleLoader()->load($autoload = true);
    }

    /**
     * @test
     */
    public function it scans and autoloads all modules including the tests directory(): void
    {
        config()->set('module-loader.development_environments', ['testing']);

        $this->getAutoloader()->shouldIgnoreMissing();
        $this->getAutoloader()->shouldReceive('addPsr4')->with('Another\\Tests\\', 'modules/Another/tests/', true)->once();

        $this->getModuleLoader()->load($autoload = true);
    }

    /**
     * @test
     */
    public function it scans and autoloads all modules including the database directory(): void
    {
        $this->getAutoloader()->shouldIgnoreMissing();

        $this->getAutoloader()->shouldReceive('add')->with('', 'modules/MyModule/database/migrations')->once();
        $this->getAutoloader()->shouldReceive('add')->with('', 'modules/MyModule/database/seeds')->once();

        $this->getModuleLoader()->load($autoload = true);
    }

    protected function dumpautoload(): void
    {
        // Disable automatic autoloading from composer.json
    }
}
