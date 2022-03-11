<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class MatchRule extends Rule
{
    public function __construct(string $pattern, bool $expected = true)
    {
        parent::__construct(
            'This value is not match',
            static fn($value) => $expected === !!preg_match($pattern, $value),
        );
    }
}
