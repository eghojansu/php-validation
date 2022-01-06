<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Distinct extends Rule
{
    protected $message = 'This value should not have any duplicated values';
    protected $iterable = false;

    protected function doValidate($value)
    {
        return count(array_unique($value)) === count($value);
    }
}
