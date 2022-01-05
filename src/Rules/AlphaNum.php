<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class AlphaNum extends Rule
{
    protected $message = 'This value should be entirely alpha-numeric characters';

    protected function doValidate($value)
    {
        return !!preg_match('/^[[:alnum:]]+$/', $value);
    }
}
