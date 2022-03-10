<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Date extends Rule
{
    public function __construct(string $format = null)
    {
        parent::__construct(
            'This value should be a date' . ($format ? ' with format ' . $format : null),
            static fn($value) => null !== ($format ? Helper::toDateFromFormat($format, $value) : Helper::toDate($value)),
        );
    }
}
