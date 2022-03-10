<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class StringRule extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be a string',
            fn() => $this->context->isValueType('string'),
        );
    }
}
