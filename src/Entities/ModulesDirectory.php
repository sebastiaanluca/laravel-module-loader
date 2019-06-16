<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Entities;

final class ModulesDirectory extends Entity
{
    /**
     * @var string
     */
    public $relativePath;

    /**
     * @var string
     */
    public $absolutePath;

    /**
     * @var string|null
     */
    public $namespace;

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return file_exists($this->absolutePath);
    }
}
