<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Utils\Str;
use Ekok\Validation\Rule;

class EndsWith extends Rule
{
    public function __construct(string $suffix, string ...$suffixes)
    {
        parent::__construct(
            'This value should ends with ' . ($suffixes ? 'one of ' . $suffix . ', ' . implode(', ', $suffixes) : $suffix),
            static fn($value) => !!Str::endsWith($value, $suffix, ...$suffixes),
        );
    }
}
