<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class ArrayRule extends Rule
{
    protected $message = 'This value should be an array';
    protected $keys;

    public function __construct(string ...$keys)
    {
        $this->keys = $keys;
    }

    protected function prepare()
    {
        if ($this->keys) {
            $this->message .= ' which contains all of these keys: ' . implode(', ', $this->keys);
        }
    }

    protected function doValidate($value)
    {
        return $this->context->isValueType('array') && (!$this->keys || !array_diff($this->keys, array_keys($value)));
    }
}
