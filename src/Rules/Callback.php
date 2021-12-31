<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Callback extends Rule
{
    public function __construct(private \Closure $cb)
    {}

    protected function doValidate()
    {
        $call = $this->cb;

        return $call($this->context, $this->result);
    }
}
