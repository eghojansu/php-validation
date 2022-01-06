<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Filled extends Rule
{
    protected $message = 'This value should be filled';

    protected function doValidate($value)
    {
        return !isset($this->result[$this->context->field]) || !in_array(
            $this->result[$this->context->field],
            array('', null),
            true,
        );
    }
}
