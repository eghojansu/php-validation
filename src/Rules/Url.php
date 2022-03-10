<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Url extends Rule
{
    public function __construct(string ...$components)
    {
        $this->components = array();
        parent::__construct(
            'This value should be a valid URL',
            static fn($value) => null !== filter_var(
                $value,
                FILTER_VALIDATE_URL,
                array_reduce(
                    $components,
                    fn (int $flags, string $comp) => $flags | (defined($flag = 'FILTER_FLAG_' . strtoupper($comp) . '_REQUIRED') ? constant($flag) : 0),
                    FILTER_NULL_ON_FAILURE,
                ),
            ),
        );
    }
}
