<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Utils\Str;
use Ekok\Validation\Rule;

class StartsWith extends Rule
{
    public function __construct(string $prefix, string ...$prefixes)
    {
        parent::__construct(
            'This value should starts with ' . ($prefixes ? 'one of ' . $prefix . ', ' . implode(', ', $prefixes) : $prefix),
            static fn($value) => !!Str::startsWith($value, $prefix, ...$prefixes),
        );
    }
}
