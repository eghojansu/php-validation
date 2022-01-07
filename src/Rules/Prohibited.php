<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Prohibited extends Rule
{
    protected $message = 'This value should be empty or not present';

    protected function doValidate($value)
    {
        return !isset($this->result[$this->context->field]) || Val::isEmpty($value);
    }
}
