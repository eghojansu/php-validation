<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class DigitsBetween extends Rule
{
    protected $message = 'This value should be entirely digits characters';

    public function __construct(private int $min, private int $max)
    {}

    protected function prepare()
    {
        $this->message .= ' with length between ' . $this->min . ' and ' . $this->max . ' characters';
    }

    protected function doValidate()
    {
        return preg_match('/^[[:digit:]]+$/', $this->context->value) && (
            ($len = strlen($this->context->value)) >= $this->min
            && $len <= $this->max
        );
    }
}
