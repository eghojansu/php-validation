<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class MatchRule extends Rule
{
    protected $message = 'This value is not match';

    public function __construct(private string $pattern, private bool $expected = true)
    {}

    protected function doValidate($value)
    {
        return $this->expected === !!preg_match($this->pattern, $value);
    }
}
