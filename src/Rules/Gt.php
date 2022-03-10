<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Gt extends Rule
{
    public function __construct(private string $field, private string|null $label = null, private bool $equals = false)
    {}

    public function getMessage(): string
    {
        return 'This value should be greater than ' . ($this->equals ? 'or equals to ' : '') . ($this->label ?? $this->field);
    }

    protected function doValidate($value)
    {
        $other = $this->result->other($this->field, $this->context->position);
        $otherType = gettype($other);
        $type = $this->context->getValueType();

        return $otherType === $type && ($this->equals ?
            Helper::toSize($value, $type) >= Helper::toSize($other, $otherType) :
            Helper::toSize($value, $type) > Helper::toSize($other, $otherType)
        );
    }
}
