<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class ProhibitedUnless extends Rule
{
    public function __construct(string $field, $value)
    {
        parent::__construct(
            'This value should be empty or not present',
            fn($val) => $value != $this->result->other($field, $this->context->position) && (
                !isset($this->result[$this->context->field]) || Val::isEmpty($val)
            ),
        );
    }
}
