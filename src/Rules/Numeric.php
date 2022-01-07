<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Numeric extends Rule
{
    protected function doValidate($value)
    {
        return is_numeric($value);
    }
}
