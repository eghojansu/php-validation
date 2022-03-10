<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Size extends Rule
{
    public function __construct(int|float $size)
    {
        parent::__construct(
            'This value size should be exactly ' . $size,
            fn($value) => $size === Helper::toSize($value, $this->context->getValueType()),
        );
    }
}
