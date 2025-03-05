<?php

namespace Blaspsoft\Doxswap\Exceptions;

use Exception;

class UnsupportedMimeTypeException extends Exception
{
    public function __construct(string $extension)
    {
        $message = "Unsupported MIME type for extension: {$extension}";
        parent::__construct($message, 0); // Note the integer 0 for code
    }
}