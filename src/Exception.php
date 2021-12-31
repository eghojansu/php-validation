<?php

namespace Ekok\Validation;

use Exception as GlobalException;

class Exception extends GlobalException
{
    public function __construct(
        public array $errors,
        string $message = null,
        int $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($message ?? 'Unprocessable entity', $code, $previous);
    }
}
