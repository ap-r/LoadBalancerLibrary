<?php

namespace App\Exception;

class LoadExceededException extends \RuntimeException
{
    public function __construct(string $message = 'Load exceeded maximum allowed value', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
