<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Different extends Rule
{
    public function __construct(private string $field, private string|null $label = null)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be different from ' . ($this->label ?? $this->field);
    }

    protected function doValidate($value)
    {
        return $value !== $this->result->other($this->field, $this->context->position);
    }
}
