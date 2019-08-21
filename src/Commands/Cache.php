<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Commands;

use Illuminate\Console\Command;
use SebastiaanLuca\Module\Services\ModuleLoader;

class Cache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster module loading';

    /**
     * @var \SebastiaanLuca\Module\Services\ModuleLoader
     */
    protected $loader;

    /**
     * Create a new command instance.
     *
     * @param \SebastiaanLuca\Module\Services\ModuleLoader $loader
     */
    public function __construct(ModuleLoader $loader)
    {
        parent::__construct();

        $this->loader = $loader;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        $modules = $this->loader->scan();
        $cache = $this->loader->getCachePath();

        $providers = $this->loader->getProviders($modules);

        file_put_contents(
            $cache,
            '<?php return ' . var_export($providers, true) . ';'
        );

        $this->info('Module service providers cached!');
    }
}
