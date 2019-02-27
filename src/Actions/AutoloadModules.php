<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

class AutoloadModules
{

    // require base_path('vendor/autoload.php')

    public function __construct()
    {

    }

    //    /**
    //     * @var \Composer\Autoload\ClassLoader
    //     */
    //    private $autoloader;

    public function execute(array $modules) : void
    {
        foreach ($this->getModules() as $name => $path) {
            $this->autoloadModule($name, $path);
        }
    }

    /**
     * @param string $name
     * @param string $path
     */
    // TODO: move to action
    private function autoloadModule(string $name, string $path) : void
    {
        $psrPath = $this->getCleanPath($path);

        // TODO: update when module group has different namespace
        $this->autoloader->addPsr4(
            $name . '\\',
            $psrPath . '/src/',
            true
        );

        // TODO: update when module group has different namespace
        if (is_dir($testsPath = $path . '/tests') && app()->environment($this->config['development_environments'])) {
            $this->autoloader->addPsr4(
                $name . '\\Tests\\',
                $psrPath . '/tests/',
                true
            );
        }

        if (is_dir($path . '/database')) {
            $this->autoloadClassmap($path . '/database');
        }
    }

    /**
     * @param string $path
     */
    // TODO: move to action
    private function autoloadClassmap(string $path) : void
    {
        $classmap = $this->files->directories($path);

        // Recursively load all non-namespaced classes
        foreach ($classmap as $directory) {
            $this->autoloader->add('', $this->getCleanPath($directory));
        }
    }
}
