<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class ArrayRule extends Rule
{
    protected $message = 'This value should be an array';

    public function __construct(string ...$keys)
    {}

    protected function prepare()
    {
        if ($this->keys) {
            $this->message .= ' which contains ' . implode(', ', $this->keys);
        }
    }

    protected function doValidate()
    {
        return is_array($this->context->value) && (
            !$this->keys
            || count(array_intersect($this->keys, array_keys($this->context->value))) === count($this->keys)
        );
    }
}
