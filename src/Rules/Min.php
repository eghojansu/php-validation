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
        $this->message = 'This value is too ' . Helper::toSizeLower($this->context->value, $this->context->type()) . '. Minimum value allowed is ' . $this->min;
    }

    protected function doValidate($value)
    {
        return Helper::toSize($value, $this->context->type()) >= $this->min;
    }
}
