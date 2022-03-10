<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class IntRule extends Rule
{
    public function __construct(bool $octal = true, bool $hex = true)
    {
        parent::__construct(
            'This value should be an integer',
            fn($value) => $this->context->updateIf(
                null !== ($update = filter_var(
                    $value,
                    FILTER_VALIDATE_INT,
                    ($octal ? FILTER_FLAG_ALLOW_OCTAL : 0) | ($hex ? FILTER_FLAG_ALLOW_HEX : 0) | FILTER_NULL_ON_FAILURE,
                )),
                $update,
            ),
        );
    }
}
