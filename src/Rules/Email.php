<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Email extends Rule
{
    protected $message = 'This value is not a valid email address';

    public function __construct(private bool $unicode = false)
    {}

    protected function doValidate($value)
    {
        return !!filter_var(
            $value,
            FILTER_VALIDATE_EMAIL,
            $this->unicode ? FILTER_FLAG_EMAIL_UNICODE : 0,
        );
    }
}
