<?php

declare(strict_types=1);

namespace Ekok\Validation;

class ValidationException extends \Exception
{
    public function __construct(
        string $message = null,
        public Result|null $result = null,
        int $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($message ?? 'Unprocessable entity', $code, $previous);
    }
}
