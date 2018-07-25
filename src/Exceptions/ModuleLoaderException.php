<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Exceptions;

use RuntimeException;

class ModuleLoaderException extends RuntimeException
{
    /**
     * @param string $name
     *
     * @return \SebastiaanLuca\Module\Exceptions\ModuleLoaderException
     */
    public static function duplicate(string $name) : self
    {
        return new static(sprintf(
            'A module named "%s" already exists.',
            [$name]
        ));
    }
}
