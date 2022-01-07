<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Filled extends Rule
{
    protected $message = 'This value should be filled';

    protected function doValidate($value)
    {
        return !isset($this->result[$this->context->field]) || Val::isEmpty($this->result[$this->context->field], false);
    }
}
