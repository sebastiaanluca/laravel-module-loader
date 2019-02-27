<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Factories;

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
            'name' => studly_case(basename($path)),
            'absolute_path' => $path,
            'directory' => $directory,
            //            'namespace' => static::getModuleNamespace($name, $directory),
            //            'service_provider_name' => $provider = static::getServiceProviderName($name),
            //            'service_provider_path' => static::getServiceProviderPath($path, $name),
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
     * @param string $name
     *
     * @return string
     */
    private static function getServiceProviderName(string $name) : string
    {
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
