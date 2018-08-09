<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Exceptions;

use Exception;

class JsonException extends Exception
{
    /**
     * @param string $error
     *
     * @return static
     */
    public static function invalidJson(string $error) : self
    {
        return new static('Unable to parse JSON: ' . strtolower($error));
    }
}
