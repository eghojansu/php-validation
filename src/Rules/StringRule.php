<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class StringRule extends Rule
{
    protected $message = 'This value should be a string';

    protected function doValidate($value)
    {
        return $this->context->isValueType('string');
    }
}
