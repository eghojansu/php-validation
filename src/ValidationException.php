<?php

namespace Ekok\Validation;

class ValidationException extends \Exception
{
    public $errors = array();

    public function __construct(
        string $message = null,
        array $errors = null,
        int $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($message ?? 'Unprocessable entity', $code, $previous);

        $this->errors = $errors ?? array();
    }
}
