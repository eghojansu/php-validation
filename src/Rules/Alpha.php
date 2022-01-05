<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Alpha extends Rule
{
    protected $message = 'This value should be entirely alphabetic characters';

    protected function doValidate($value)
    {
        return !!preg_match('/^[[:alpha:]]+$/', $value);
    }
}
