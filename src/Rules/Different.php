<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Different extends Rule
{
    public function __construct(private string $field)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be different from ' . $this->field;
    }

    protected function doValidate()
    {
        return $this->context->value != $this->result->other($this->field, $this->context->position);
    }
}
