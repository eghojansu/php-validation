<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Validation\Rule;

class InArray extends Rule
{
    public function __construct(private string $field, private string|null $label = null, private bool $strict = false)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be within value of ' . ($this->label ?? $this->field);
    }

    protected function doValidate($value)
    {
        return Arr::includes((array) $this->result[$this->field], $value, $this->strict);
    }
}
