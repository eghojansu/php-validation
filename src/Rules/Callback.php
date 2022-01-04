<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Callback extends Rule
{
    public function __construct(private \Closure $cb)
    {}

    protected function doValidate()
    {
        return $this->cb->call($this, $this->context, $this->result);
    }
}
