<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class ArrayRule extends Rule
{
    public function __construct(string ...$keys)
    {
        parent::__construct(
            'This value should be an array' . (
                $keys ? ' which contains all of these keys: ' . implode(', ', $keys) : ''
            ),
            fn($value) => $this->context->isValueType('array') && (!$keys || !array_diff($keys, array_keys($value))),
        );
    }
}
