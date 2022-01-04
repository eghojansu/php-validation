<?php

namespace Ekok\Validation;

class ValidationException extends \Exception
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
