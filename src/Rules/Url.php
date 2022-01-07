<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Url extends Rule
{
    protected $message = 'This value should be a valid URL';
    private $components = array();

    public function __construct(string ...$components)
    {
        $this->components = array();
    }

    protected function doValidate($value)
    {
        return null !== filter_var(
            $value,
            FILTER_VALIDATE_URL,
            array_reduce(
                $this->components,
                fn (int $flags, string $comp) => $flags | (defined($flag = 'FILTER_FLAG_' . strtoupper($comp) . '_REQUIRED') ? constant($flag) : 0),
                FILTER_NULL_ON_FAILURE,
            ),
        );
    }
}
