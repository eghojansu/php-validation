<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Alpha extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be entirely alphabetic characters',
            static fn($value) => !!preg_match('/^[[:alpha:]]+$/', $value),
        );
    }
}
