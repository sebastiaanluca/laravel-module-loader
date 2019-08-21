<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RefreshModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:refresh
                            {--K|keep : Keep existing module autoload entries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan your project for modules, write their autoload config to composer.json, and refresh composer autoloading';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        $this->call('modules:autoload', [
            '--keep' => $this->option('keep'),
        ]);

        $process = new Process(
            ['composer', 'dumpautoload'],
            base_path()
        );

        $process->start();

        foreach ($process as $type => $data) {
            echo $data;
        }

        $this->info('Modules refreshed');
    }
}
