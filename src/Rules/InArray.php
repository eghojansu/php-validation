<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Validation\Rule;

class InArray extends Rule
{
    public function __construct(string $field, string $label = null, bool $strict = false)
    {
        parent::__construct(
            'This value should be within value of ' . ($label ?? $field),
            fn($value) => Arr::includes((array) $this->result[$field], $value, $strict),
        );
    }
}
