<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class AlphaNum extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be entirely alpha-numeric characters',
            static fn($value) => !!preg_match('/^[[:alnum:]]+$/', $value),
        );
    }
}
