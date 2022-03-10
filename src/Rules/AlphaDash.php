<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class AlphaDash extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should contains only alpha-numeric characters, dashes or underscores',
            static fn($value) => !!preg_match('/^[[:alnum:]\-_]+$/', $value),
        );
    }
}
