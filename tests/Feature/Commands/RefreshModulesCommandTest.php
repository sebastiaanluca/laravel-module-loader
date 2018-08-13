<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use Mockery;
use SebastiaanLuca\Module\Commands\RefreshModules;
use SebastiaanLuca\Module\Tests\TestCase;

class RefreshModulesCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it scans and updates all modules() : void
    {
        $command = Mockery::mock(RefreshModules::class . '[call]');

        $command->shouldReceive('call')->once()->with('modules:autoload', ['--keep' => false]);

        app(Kernel::class)->registerCommand($command);

        $this->expectOutputRegex('/Generating autoload files/');

        $this->artisan('modules:refresh');
    }
}
