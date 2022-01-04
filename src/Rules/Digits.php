<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Digits extends Rule
{
    protected $message = 'This value should be entirely digits characters';

    public function __construct(private int|null $length = null)
    {}

    protected function prepare()
    {
        if ($this->length) {
            $this->message .= ' within ' . $this->length . ' characters';
        }
    }

    protected function doValidate()
    {
        return preg_match('/^[[:digit:]]+$/', $this->context->value) && (
            !$this->length || strlen($this->context->value) === $this->length
        );
    }
}
