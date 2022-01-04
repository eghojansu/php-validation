<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Boolean extends Rule
{
    protected $message = 'This value should be able to cast as boolean';

    protected function doValidate()
    {
        return null !== Helper::toBool($this->context->value);
    }
}
