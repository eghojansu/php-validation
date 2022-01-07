<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Min extends Rule
{
    public function __construct(private int|float $min)
    {}

    protected function prepare()
    {
        $this->message = 'This value is too low. Minimum value allowed is ' . $this->min;
    }

    protected function doValidate($value)
    {
        return Helper::toSize($value, $this->context->type()) >= $this->min;
    }
}
