<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Entities;

final class ModulesDirectory extends Entity
{
    /**
     * @var string
     */
    public $relative_path;

    /**
     * @var string
     */
    public $absolute_path;

    /**
     * @var string|null
     */
    public $namespace;

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return file_exists($this->absolute_path);
    }
}
