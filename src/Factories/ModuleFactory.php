<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Factories;

use Illuminate\Support\Str;
use SebastiaanLuca\Module\Entities\Module;
use SebastiaanLuca\Module\Entities\ModulesDirectory;

class ModuleFactory
{
    /**
     * @param string $path
     * @param \SebastiaanLuca\Module\Entities\ModulesDirectory $directory
     *
     * @return \SebastiaanLuca\Module\Entities\Module
     */
    public static function createFromPathAndDirectory(string $path, ModulesDirectory $directory) : Module
    {
        return new Module([
            'name' => $name = Str::studly(basename($path)),
            'absolutePath' => $path,
            'relativePath' => $directory->relativePath . DIRECTORY_SEPARATOR . $name,
            'directory' => $directory,
            'namespace' => static::getModuleNamespace($name, $directory),
            'serviceProviderPath' => $providerPath = static::getServiceProviderPath($path, $name),
            'serviceProviderName' => static::getServiceProviderName($providerPath),
        ]);
    }

    /**
     * @param string $name
     * @param \SebastiaanLuca\Module\Entities\ModulesDirectory $directory
     *
     * @return string
     */
    private static function getModuleNamespace(string $name, ModulesDirectory $directory) : string
    {
        if ($directory->namespace !== null) {
            return $directory->namespace . '\\' . $name;
        }

        return $name;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private static function getServiceProviderName(string $path) : string
    {
        return basename($path, '.php');
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    private static function getServiceProviderPath(string $path, string $name) : string
    {
        return sprintf(
            '%s/src/Providers/%sServiceProvider.php',
            $path,
            $name
        );
    }
}
