<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Numeric extends Rule
{
    protected function doValidate($value)
    {
        $passed = is_numeric($value);

        if ($passed) {
            $this->context->value = $value * 1;
        }

        return $passed;
    }
}
