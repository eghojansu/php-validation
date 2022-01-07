<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Max extends Rule
{
    public function __construct(private int|float $max)
    {}

    protected function prepare()
    {
        $this->message = 'This value is too high. Maximum value allowed is ' . $this->max;
    }

    protected function doValidate($value)
    {
        return Helper::toSize($value, $this->context->type()) <= $this->max;
    }
}
