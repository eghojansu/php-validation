<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class ActiveUrl extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value is not an active URL',
            static fn($value) => ($host = parse_url($value ?? '', PHP_URL_HOST)) && dns_get_record($host, DNS_A|DNS_AAAA),
        );
    }
}
