<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Nullable extends Rule
{
    protected function doValidate($value)
    {
        if (Val::isEmpty($value)) {
            $this->context->stopPropagation();
        }

        return true;
    }
}
