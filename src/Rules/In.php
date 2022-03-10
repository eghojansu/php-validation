<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Validation\Rule;

class In extends Rule
{
    public function __construct(...$choices)
    {
        parent::__construct(
            'This value should be one of these values: ' . implode(', ', $choices),
            static fn($value) => Arr::includes($choices, $value),
        );
    }
}
