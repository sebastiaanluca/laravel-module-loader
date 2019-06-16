<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

use SebastiaanLuca\Module\Entities\Module;

class DetermineProvider
{
    /**
     * @var \SebastiaanLuca\Module\Entities\Module
     */
    private $module;

    /**
     * @param \SebastiaanLuca\Module\Entities\Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * @return string|null
     */
    public function execute() : ?string
    {
        //        $path = "{$this->module->path}/src/Providers/{$this->module->name}ServiceProvider.php";

        $path = $this->module->serviceProviderPath;

        if (! file_exists($path)) {
            return null;
        }

        $path = $this->getCleanPath($path);

        $find = [
            $this->module->directory->relativePath . DIRECTORY_SEPARATOR . $this->module->name . DIRECTORY_SEPARATOR . 'src',
            DIRECTORY_SEPARATOR,
            '.php',
        ];

        $replace = [
            $this->module->directory->namespace . '\\' . $this->module->name,
            '\\',
            '',
        ];

        $provider = str_replace($find, $replace, $path);

        // Do not register providers that don't exist
        if (! class_exists($provider)) {
            return null;
        }

        return $provider;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getCleanPath(string $path) : string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
