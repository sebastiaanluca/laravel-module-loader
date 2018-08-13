<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use ArrayIterator;
use Illuminate\Contracts\Console\Kernel;
use IteratorAggregate;
use Mockery;
use SebastiaanLuca\Module\Commands\RefreshModules;
use SebastiaanLuca\Module\Tests\TestCase;
use Symfony\Component\Process\Process;
use Traversable;

class RefreshModulesCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it scans and updates all modules() : void
    {
        $this->app->bind('module-loader.process', function ($app, $args) {
            $this->assertSame([sprintf('cd %s && composer dumpautoload', base_path())], $args);

            $mock = Mockery::mock(Process::class . '[start,getIterator]', $args);

            $mock->shouldReceive('start')->once();
            $mock->shouldReceive('getIterator')->andReturn($this->getMockedIterator());

            return $mock;
        });

        $command = Mockery::mock(RefreshModules::class . '[call]');

        $command->shouldReceive('call')->once()->with('modules:autoload', ['--keep' => false]);

        app(Kernel::class)->registerCommand($command);

        $this->expectOutputRegex('/Generating autoload files/');

        $this->artisan('modules:refresh');
    }

    /**
     * @return \Traversable
     */
    private function getMockedIterator() : Traversable
    {
        return new class implements IteratorAggregate
        {
            /**
             * Retrieve an external iterator
             *
             * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
             * @return Traversable An instance of an object implementing <b>Iterator</b> or
             * <b>Traversable</b>
             * @since 5.0.0
             */
            public function getIterator() : Traversable
            {
                return new ArrayIterator([
                    'Generating autoload files',
                ]);
            }
        };
    }
}
