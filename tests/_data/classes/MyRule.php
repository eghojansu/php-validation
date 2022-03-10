<?php

use Ekok\Validation\Rule;

class MyRule extends Rule
{
    protected function doValidate($value)
    {
        return trim($value);
    }
}
