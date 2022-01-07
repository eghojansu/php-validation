<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Timezone extends Rule
{
    protected $message = 'This value should be a valid Timezone';

    public function __construct(private string|null $group = null, private string|null $country = null)
    {}

    protected function doValidate($value)
    {
        return in_array(
            $value,
            timezone_identifiers_list(
                $this->group && defined($group = 'DateTimeZone::' . strtoupper($this->group)) ? constant($group) : \DateTimeZone::ALL,
                $this->country,
            ),
        );
    }
}
