<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Confirmed extends Rule
{
    protected $message = 'This value should be confirmed';

    public function __construct(private string|null $field = null)
    {}

    protected function doValidate()
    {
        return $this->context->value === $this->result->other($this->field ?? $this->context->field . '_confirmation', $this->context->position);
    }
}
