<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use Mockery;
use SebastiaanLuca\Module\Commands\RegisterModuleAutoloading;
use SebastiaanLuca\Module\Services\ModuleLoader;
use SebastiaanLuca\Module\Tests\TestCase;

class RegisterModuleAutoloadingCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it scans all modules and writes the composer autoload config(): void
    {
        unlink(base_path('composer.json'));

        @mkdir(base_path('modules/Another/database/factories'), 0777, true);
        @mkdir(base_path('modules/Another/database/seeds'), 0777, true);

        $loader = Mockery::mock(ModuleLoader::class);

        $loader->shouldReceive('scan')->andReturn([
            'Another' => base_path('modules/Another'),
        ]);

        $command = Mockery::mock(RegisterModuleAutoloading::class.'[info]', [$loader]);

        $command->shouldReceive('info')->once()->with('Wrote composer.json autoload configuration for 1 modules');

        app(Kernel::class)->registerCommand($command);

        $this->artisan('modules:autoload');
    }

    /**
     * @test
     */
    public function it scans all modules and overwrites the existing composer autoload config(): void
    {
        @mkdir(base_path('modules/Extra/tests'), 0777, true);

        $loader = Mockery::mock(ModuleLoader::class);

        $loader->shouldReceive('scan')->andReturn([
            'MyModule' => base_path('modules/MyModule'),
            'Extra' => base_path('modules/Extra'),
        ]);

        $command = Mockery::mock(RegisterModuleAutoloading::class.'[info]', [$loader]);

        $command->shouldReceive('info')->once()->with('Wrote composer.json autoload configuration for 2 modules');

        app(Kernel::class)->registerCommand($command);

        $this->artisan('modules:autoload');

        $config = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_OBJECT_AS_ARRAY | JSON_UNESCAPED_SLASHES);

        $expected = [
            'autoload' => [
                'psr-4' => [
                    'Extra\\' => 'modules/Extra/src/',
                    'MyModule\\' => 'modules/MyModule/src/',
                ],
                'classmap' => [
                    'modules/MyModule/database/migrations',
                    'modules/MyModule/database/seeds',
                ],
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Extra\\Tests\\' => 'modules/Extra/tests/',
                ],
            ],
        ];

        static::assertSame($expected, $config);
    }

    /**
     * @test
     */
    public function it scans all modules and updates the existing composer autoload config(): void
    {
        @mkdir(base_path('modules/Extra/tests'), 0777, true);

        $loader = Mockery::mock(ModuleLoader::class);

        $loader->shouldReceive('scan')->andReturn([
            'MyModule' => base_path('modules/MyModule'),
            'Extra' => base_path('modules/Extra'),
        ]);

        $command = Mockery::mock(RegisterModuleAutoloading::class.'[info]', [$loader]);

        $command->shouldReceive('info')->once()->with('Wrote composer.json autoload configuration for 2 modules');

        app(Kernel::class)->registerCommand($command);

        $this->artisan('modules:autoload', ['--keep' => true]);

        $config = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_OBJECT_AS_ARRAY | JSON_UNESCAPED_SLASHES);

        $expected = [
            'autoload' => [
                'psr-4' => [
                    'Another\\' => 'modules/Another/src/',
                    'Extra\\' => 'modules/Extra/src/',
                    'Missing\\' => 'modules/Missing/src/',
                    'MyModule\\' => 'modules/MyModule/src/',
                ],
                'classmap' => [
                    'modules/MyModule/database/migrations',
                    'modules/MyModule/database/seeds',
                ],
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Extra\\Tests\\' => 'modules/Extra/tests/',
                ],
            ],
        ];

        static::assertSame($expected, $config);
    }
}
