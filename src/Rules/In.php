<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Validation\Rule;

class In extends Rule
{
    private $choices = array();

    public function __construct(...$choices)
    {
        $this->choices = $choices;
    }

    protected function prepare()
    {
        $this->message = 'This value should be one of these values: ' . implode(', ', $this->choices);
    }

    protected function doValidate($value)
    {
        return Arr::includes($this->choices, $value);
    }
}
