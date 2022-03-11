<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Digits extends Rule
{
    public function __construct(int $min = null, int $max = null)
    {
        parent::__construct(
            'This value should be entirely digits characters' . match(true) {
                $max && $min => ' with length between ' . $min . ' and ' . $max . ' characters',
                $min => ' within ' . $min . ' characters',
                default => null,
            },
            static fn($value) => preg_match('/^[[:digit:]]+$/', "{$value}") && (
                null === $min
                || ($len = strlen("{$value}")) === $min
                || ($max && ($len >= $min && $len <= $max))
            ),
        );
    }
}
