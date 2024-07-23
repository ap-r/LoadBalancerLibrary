<?php

namespace App\Exception;

use RuntimeException;

class LoadExceededException extends RuntimeException
{
    public function __construct($message = "Load exceeded maximum allowed value", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
