<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Present extends Rule
{
    protected function doValidate($value)
    {
        return isset($value) || isset($this->result[$this->context->field]);
    }
}
