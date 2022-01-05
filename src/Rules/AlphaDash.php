<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class AlphaDash extends Rule
{
    protected $message = 'This value should contains only alpha-numeric characters, dashes or underscores';

    protected function doValidate($value)
    {
        return !!preg_match('/^[[:alnum:]\-_]+$/', $value);
    }
}
