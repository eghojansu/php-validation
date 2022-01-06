<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Str;
use Ekok\Validation\Rule;

class EndsWith extends Rule
{
    private $suffixes;

    public function __construct(string ...$suffixes)
    {
        $this->suffixes = $suffixes;
    }

    protected function prepare()
    {
        $this->message = 'This value should ends with ';

        if (isset($this->suffixes[1])) {
            $this->message .= 'one of ' . implode(', ', $this->suffixes);
        } else {
            $this->message .= $this->suffixes[0];
        }
    }

    protected function doValidate($value)
    {
        return !!Str::endsWith($value, ...$this->suffixes);
    }
}
