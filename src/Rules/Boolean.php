<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;
use Ekok\Validation\Helper;

class Boolean extends Rule
{
    protected $message = 'This value should be able to cast as boolean';

    protected function doValidate($value)
    {
        $passed = (null !== ($update = Helper::toBool($value)));

        if ($passed) {
            $this->context->value = $update;
        }

        return $passed;
    }
}
