<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class RequiredUnless extends Rule
{
    protected $message = 'This value should not empty';

    public function __construct(private string $field, private $value)
    {}

    protected function doValidate($value)
    {
        return $this->value != $this->result->other($this->field, $this->context->position) && Val::isEmpty($value, false);
    }
}
