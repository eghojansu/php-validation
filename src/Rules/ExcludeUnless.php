<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class ExcludeUnless extends Rule
{
    public function __construct(private string $field, private $value)
    {}

    protected function doValidate($value)
    {
        if ($this->value != $this->result->other($this->field, $this->context->position)) {
            $this->context->ignoreValue()->stopPropagation();
        }

        return true;
    }
}
