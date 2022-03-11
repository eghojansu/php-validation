<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Email extends Rule
{
    public function __construct(bool $unicode = false)
    {
        parent::__construct(
            'This value is not a valid email address',
            static fn($value) => !!filter_var(
                $value,
                FILTER_VALIDATE_EMAIL,
                $unicode ? FILTER_FLAG_EMAIL_UNICODE : 0,
            ),
        );
    }
}
