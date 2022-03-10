<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Between extends Rule
{
    public function __construct(int|float $min, int|float $max)
    {
        parent::__construct(
            'This value should between ' . $min . ' and ' . $max,
            fn($value) => ($size = Helper::toSize($value, $this->context->getValueType())) >= $min && $size <= $max,
        );
    }
}
