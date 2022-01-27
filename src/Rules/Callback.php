<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Callback extends Rule
{
    public function __construct(private \Closure $cb, string $message = null)
    {
        if ($message) {
            $this->setMessage($message);
        }
    }

    protected function doValidate($value)
    {
        return $this->cb->call($this, $value, ...array_values($this->params));
    }
}
