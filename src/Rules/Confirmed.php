<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Confirmed extends Rule
{
    public function __construct(string $field = null)
    {
        parent::__construct(
            'This value should be confirmed',
            fn($value) => $value == $this->result->other($field ?? $this->context->field . '_confirmation', $this->context->position),
        );
    }
}
