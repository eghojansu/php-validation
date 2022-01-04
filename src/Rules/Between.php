<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Between extends Rule
{
    public function __construct(private int|float $min, private int|float $max)
    {}

    protected function prepare()
    {
        $this->message = 'This value should between ' . $this->min . ' and ' . $this->max;
    }

    protected function doValidate()
    {
        $size = Helper::toSize($this->context->value, $this->context->getType());

        return $size >= $min && $size <= $max;
    }
}
