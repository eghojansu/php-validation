<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Gt extends Rule
{
    public function __construct(private string $field, private string|null $label = null, private bool $equals = false)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be greater than ' . ($this->label ?? $this->field);
    }

    protected function doValidate($value)
    {
        return !isset($this->result[$this->context->field]) || !in_array(
            $this->result[$this->context->field],
            array('', null),
            true,
        );
    }
}
