<?php

namespace SebastiaanLuca\Module\Commands;

use Illuminate\Console\Command;
use SebastiaanLuca\Module\Services\ModuleLoader;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the module loader cache file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        @unlink(ModuleLoader::getCachePath());

        $this->info('Module service providers cache cleared!');
    }
}
