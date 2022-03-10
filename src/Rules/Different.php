<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Different extends Rule
{
    public function __construct(string $field, string $label = null)
    {
        parent::__construct(
            'This value should be different from ' . ($label ?? $field),
            fn($value) => $value != $this->result->other($field, $this->context->position),
        );
    }
}
