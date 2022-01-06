<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class DateEquals extends Rule
{
    public function __construct(private string $date, private string|null $label = null)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be equals to ' . ($this->label ?? $this->date);
    }

    protected function doValidate($value)
    {
        return (
            ($a = Helper::toDate($value))
            && ($b = Helper::toDate($this->result->other($this->date, $this->context->position)) ?? Helper::toDate($this->date))
            && ($a == $b)
        );
    }
}
