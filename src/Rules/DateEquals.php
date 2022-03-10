<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class DateEquals extends Rule
{
    public function __construct(string $date, string $label = null)
    {
        parent::__construct(
            'This value should be equals to ' . ($label ?? $date),
            fn($value) => (
                ($a = Helper::toDate($value))
                && ($b = Helper::toDate($this->result->other($date, $this->context->position)) ?? Helper::toDate($date))
                && ($a == $b)
            ),
        );
    }
}
