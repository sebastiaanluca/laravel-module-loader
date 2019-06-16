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
    public $absolutePath;

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
    public $serviceProviderName;

    /**
     * @var string
     */
    public $serviceProviderPath;

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return is_dir($this->absolutePath . '/src');
    }
}
