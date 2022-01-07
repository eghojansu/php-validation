<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Size extends Rule
{
    public function __construct(private int|float $size)
    {}

    protected function prepare()
    {
        $this->message = 'This value size should be exactly ' . $this->size;
    }

    protected function doValidate($value)
    {
        return $this->size === Helper::toSize($value, $this->context->type());
    }
}
