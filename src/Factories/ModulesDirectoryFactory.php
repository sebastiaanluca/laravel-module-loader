<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Factories;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SebastiaanLuca\Module\Entities\ModulesDirectory;

class ModulesDirectoryFactory
{
    /**
     * @param string $directory
     *
     * @return \SebastiaanLuca\Module\Entities\ModulesDirectory
     */
    public static function createFromDirectory(string $directory) : ModulesDirectory
    {
        return new ModulesDirectory([
            'relative_path' => $directory,
            'absolute_path' => base_path($directory),
            'namespace' => static::getNamespaceForDirectory($directory),
        ]);
    }

    /**
     * @param array $directories
     *
     * @return \Illuminate\Support\Collection
     */
    public static function createCollectionFromArray(array $directories) : Collection
    {
        return collect($directories)->map(function (string $directory) {
            return static::createFromDirectory($directory);
        });
    }

    /**
     * @param string $directory
     *
     * @return string|null
     */
    private static function getNamespaceForDirectory(string $directory) : ?string
    {
        return Arr::get(
            config('module-loader.namespaces'),
            $directory
        );
    }
}
