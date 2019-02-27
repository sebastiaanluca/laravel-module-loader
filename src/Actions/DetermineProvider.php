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

        $path = $this->module->service_provider_path;

        dd($path);

        if (! file_exists($path)) {
            return null;
        }

        $path = $this->getCleanPath($path);

        $find = [$this->name . '/src', '/', '.php'];
        $replace = [$this->name, '\\', ''];

        $provider = str_replace($find, $replace, $path);

        // Support multiple module directories
        $directories = array_map(function (string $path) {
            return $path . '\\';
        }, $this->config['directories']);

        $provider = str_replace($directories, [], $provider);

        // Do not register providers that don't exist or
        // don't have their namespace loaded
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
            return str_replace(base_path() . '/', '', $path);
        }
}
