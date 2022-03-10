<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Validation\Rule;

class NotIn extends Rule
{
    public function __construct(...$choices)
    {
        parent::__construct(
            'This value should not be one of these values: ' . implode(', ', $choices),
            static fn($value) => !Arr::includes($choices, $value),
        );
    }
}
