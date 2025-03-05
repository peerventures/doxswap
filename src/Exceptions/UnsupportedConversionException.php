<?php

namespace Blaspsoft\Doxswap\Exceptions;

use Exception;

class UnsupportedConversionException extends Exception
{
    public function __construct(string $fromExtension, string $toExtension)
    {
        $message = "Conversion from '{$fromExtension}' to '{$toExtension}' is not supported";
        parent::__construct($message, 0); // Note the integer 0 for code
    }
}