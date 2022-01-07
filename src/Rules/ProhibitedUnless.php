<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class ProhibitedUnless extends Rule
{
    protected $message = 'This value should be empty or not present';

    public function __construct(private string $field, private $value)
    {}

    protected function doValidate($value)
    {
        return $this->value != $this->result->other($this->field, $this->context->position) && (
            !isset($this->result[$this->context->field]) || Val::isEmpty($value)
        );
    }
}
