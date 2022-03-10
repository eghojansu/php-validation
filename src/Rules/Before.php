<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Before extends Rule
{
    public function __construct(string $date, string $label = null, bool $equals = false)
    {
        parent::__construct(
            'This value should be before ' . ($equals ? 'or equals to ' : '') . ($label ?? $date),
            fn($value) => (
                ($a = Helper::toDate($value))
                && ($b = Helper::toDate($this->result->other($date, $this->context->position)) ?? Helper::toDate($date))
                && ($equals ? $a <= $b : $a < $b)
            ),
        );
    }
}
