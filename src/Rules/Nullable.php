<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Nullable extends Rule
{
    protected function doValidate($value)
    {
        if (null === $value) {
            $this->context->stopPropagation();
        }

        return $value;
    }
}
