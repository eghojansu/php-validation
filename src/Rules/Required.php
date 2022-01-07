<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Required extends Rule
{
    protected $message = 'This value should not empty';

    public function __construct()
    {}

    protected function doValidate($value)
    {
        return Val::isEmpty($value, false);
    }
}
