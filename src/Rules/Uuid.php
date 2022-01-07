<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Uuid extends Rule
{
    protected $message = 'This value should be a valid UUID';

    protected function doValidate($value)
    {
        return !!preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $value);
    }
}
