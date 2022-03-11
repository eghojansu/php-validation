<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Ip extends Rule
{
    public function __construct(string|int $version = null, string ...$ranges)
    {
        parent::__construct(
            'This value should be an IP' . $version .' Address',
            static fn($value) => null !== filter_var(
                $value,
                FILTER_VALIDATE_IP,
                array_reduce(
                    $ranges,
                    fn (int $flags, string $range) => $flags | (defined($flag = 'FILTER_FLAG_' . strtoupper($range) . '_RANGE') ? constant($flag) : 0),
                    $version && defined($flag = 'FILTER_FLAG_IPV' . $version) ? constant($flag) : FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6,
                ) | FILTER_NULL_ON_FAILURE,
            ),
        );
    }
}
