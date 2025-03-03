<?php

namespace Blaspsoft\Doxswap\Exceptions;

use Exception;

class UnsupportedConversionException extends Exception
{
    public function __construct($message = "Unsupported conversion", $code = 0)
    {
        parent::__construct($message, $code);
    }
}