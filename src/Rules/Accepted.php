<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Accepted extends Rule
{
    protected $message = 'This value should be accepted';

    protected function doValidate($value)
    {
        return true === Helper::toBool($value);
    }
}
