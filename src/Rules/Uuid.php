<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Uuid extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be a valid UUID',
            static fn($value) => !!preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $value),
        );
    }
}
