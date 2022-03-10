<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Min extends Rule
{
    public function __construct(private int|float $min)
    {}

    public function getMessage(): string
    {
        return 'This value is too ' . Helper::toSizeLower($this->context->value, $this->context->getValueType()) . '. Minimum value allowed is ' . $this->min;
    }

    protected function doValidate($value)
    {
        return Helper::toSize($value, $this->context->getValueType()) >= $this->min;
    }
}
