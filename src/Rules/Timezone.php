<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Timezone extends Rule
{
    public function __construct(string $group = null, string $country = null)
    {
        parent::__construct(
            'This value should be a valid Timezone',
            static fn($value) => in_array(
                $value,
                timezone_identifiers_list(
                    $group && defined($grp = 'DateTimeZone::' . strtoupper($group)) ? constant($grp) : \DateTimeZone::ALL,
                    $country,
                ),
            ),
        );
    }
}
