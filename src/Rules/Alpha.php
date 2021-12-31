<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Alpha extends Rule
{
    protected $message = 'This value should be entirely alphabetic characters';

    protected function doValidate()
    {
        return !!preg_match('/^[[:alpha:]]+$/', $this->context->value);
    }
}
