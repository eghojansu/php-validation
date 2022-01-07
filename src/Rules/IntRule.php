<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class IntRule extends Rule
{
    protected $message = 'This value should be an integer';

    public function __construct(private bool $octal = true, private bool $hex = true)
    {}

    protected function doValidate($value)
    {
        return null !== filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ($this->octal ? FILTER_FLAG_ALLOW_OCTAL : 0) | ($this->hex ? FILTER_FLAG_ALLOW_HEX : 0) | FILTER_NULL_ON_FAILURE,
        );
    }
}
