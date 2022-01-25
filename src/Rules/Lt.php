<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Lt extends Rule
{
    public function __construct(private string $field, private string|null $label = null, private bool $equals = false)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be less than ';

        if ($this->equals) {
            $this->message .= 'or equals to ';
        }

        $this->message .= $this->label ?? $this->field;
    }

    protected function doValidate($value)
    {
        $other = $this->result->other($this->field, $this->context->position);
        $otherType = gettype($other);
        $type = $this->context->getValueType();

        return $otherType === $type && ($this->equals ?
            Helper::toSize($value, $type) <= Helper::toSize($other, $otherType) :
            Helper::toSize($value, $type) < Helper::toSize($other, $otherType)
        );
    }
}
