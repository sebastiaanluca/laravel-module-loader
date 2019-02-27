<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Entities;

final class Module extends Entity
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $absolute_path;

    /**
     * @var \SebastiaanLuca\Module\Entities\ModulesDirectory
     */
    public $directory;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $service_provider_name;

    /**
     * @var string
     */
    public $service_provider_path;

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return is_dir($this->absolute_path . '/src');
    }

    // TODO: auto-generate and assign namespace, service provider name, and service provider path on creation
    //  Move here from factory
}
