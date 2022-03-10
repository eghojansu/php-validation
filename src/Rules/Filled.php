<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Filled extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be filled',
            fn() => !isset($this->result[$this->context->field]) || Val::isEmpty($this->result[$this->context->field], false),
        );
    }
}
