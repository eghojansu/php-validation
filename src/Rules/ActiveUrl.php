<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class ActiveUrl extends Rule
{
    protected $message = 'This value is not an active URL';

    protected function doValidate($value)
    {
        return ($host = parse_url($value, PHP_URL_HOST)) && dns_get_record($host, DNS_A|DNS_AAAA);
    }
}
