<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Numeric extends Rule
{
    protected function doValidate($value)
    {
        return $this->context->updateIf(is_numeric($value), static fn() => $value * 1);
    }
}
