<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class AlphaDash extends Rule
{
    protected $message = 'This value should contains only alpha-numeric characters, dashes or underscores';

    protected function doValidate()
    {
        return !!preg_match('/^[[:alnum:]\-_]+$/', $this->context->value);
    }
}
