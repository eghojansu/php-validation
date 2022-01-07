<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Str;
use Ekok\Validation\Rule;

class StartsWith extends Rule
{
    private $prefixes;

    public function __construct(string ...$prefixes)
    {
        $this->prefixes = $prefixes;
    }

    protected function prepare()
    {
        $this->message = 'This value should starts with ';

        if (isset($this->prefixes[1])) {
            $this->message .= 'one of ' . implode(', ', $this->prefixes);
        } else {
            $this->message .= $this->prefixes[0];
        }
    }

    protected function doValidate($value)
    {
        return !!Str::startsWith($value, ...$this->prefixes);
    }
}
