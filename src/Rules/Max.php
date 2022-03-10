<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Max extends Rule
{
    public function __construct(private int|float $max)
    {}

    public function getMessage(): string
    {
        return 'This value is too ' . Helper::toSizeGreater($this->context->value, $this->context->getValueType()) . '. Maximum value allowed is ' . $this->max;
    }

    protected function doValidate($value)
    {
        return Helper::toSize($value, $this->context->getValueType()) <= $this->max;
    }
}
