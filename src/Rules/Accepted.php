<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Accepted extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be accepted',
            static fn($value) => !!Helper::toBool($value),
        );
    }
}
