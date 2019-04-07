<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use SebastiaanLuca\Module\Entities\ModulesDirectory;

class ScanDirectories
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
     * @param \Illuminate\Support\Collection $directories
     *
     * @return array
     */
    public function execute(Collection $directories) : array
    {
        if ($directories->isEmpty()) {
            return [];
        }

        return collect($directories)
            ->filter->isValid()
            ->flatMap(function (ModulesDirectory $directory) {
                return app(ScanModulesInDirectory::class)->execute($directory);
            })
            ->all();
    }
}
