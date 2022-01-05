<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class DateEquals extends Rule
{
    public function __construct(private string $date)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be equals to ' . $this->date;
    }

    protected function doValidate($value)
    {
        return Helper::toDate($this->date) == Helper::toDate($value);
    }
}
