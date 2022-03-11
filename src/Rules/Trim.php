<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Trim extends Rule
{
    public function __construct(string $chars = null) {
        $setup = (array) $chars;

        parent::__construct(
            null,
            fn($value) => $this->context->isValueType('string') ? trim($value, ...$setup) : $value,
        );
    }
}
