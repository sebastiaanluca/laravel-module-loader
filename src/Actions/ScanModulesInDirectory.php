<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

use Illuminate\Filesystem\Filesystem;
use SebastiaanLuca\Module\Entities\ModulesDirectory;
use SebastiaanLuca\Module\Factories\ModuleFactory;

class ScanModulesInDirectory
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $files;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @param \SebastiaanLuca\Module\Entities\ModulesDirectory $directory
     *
     * @return array
     */
    public function execute(ModulesDirectory $directory) : array
    {
        collect($this->files->directories($directory->absolute_path))
            ->map(function (string $path) use ($directory) {
                return ModuleFactory::createFromPathAndDirectory(
                    $path,
                    $directory
                );
            })
            ->filter->isValid()
            // REMOVE
            ->dd();

        //        $modules = $this->files->directories($path);

        //        foreach ($modules as $directory) {
        //            $module = ModuleFactory::createFromDirectory($directory);
        //
        //            if (array_key_exists($module->namespace, $modules)) {
        //                throw ModuleLoaderException::duplicate($module->namespace);
        //            }
        //
        //            $modules[$module->namespace] = $module;
        //        }
    }
}
