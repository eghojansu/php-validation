<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Required extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should not empty',
            static fn($value) => Val::isEmpty($value, false),
        );
    }
}
