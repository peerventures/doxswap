<?php

namespace Blaspsoft\Doxswap\Exceptions;

use Exception;

class UnsupportedMimeTypeException extends Exception
{
    public function __construct($message = "Unsupported MIME type", $code = 0)
    {
        parent::__construct($message, $code);
    }
}