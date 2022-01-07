<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Ip extends Rule
{
    protected $ranges = array();

    public function __construct(private string|int|null $version = null, string ...$ranges)
    {
        $this->ranges = $ranges;
    }

    protected function prepare()
    {
        $this->message = 'This value should be an IP' . $this->version .' Address';
    }

    protected function doValidate($value)
    {
        $flags = array_reduce(
            $this->ranges,
            fn(int $flags, string $range) => $flags | (defined($flag = 'FILTER_FLAG_NO_' . $range . '_RANGE') ? constant($flag) : 0),
            $this->version && defined($flag = 'FILTER_FLAG_IPV' . $this->version) ? constant($flag) : FILTER_FLAG_IPV4|FILTER_FLAG_IPV6,
        ) | FILTER_NULL_ON_FAILURE;

        return null !== filter_var($value, FILTER_VALIDATE_IP, $flags);
    }
}
