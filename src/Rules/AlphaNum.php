<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class AlphaNum extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be entirely alpha-numeric characters',
            static fn($value) => $value && preg_match('/^[[:alnum:]]+$/', $value),
        );
    }
}
