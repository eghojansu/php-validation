<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class Date extends Rule
{
    public function __construct(private string|null $format = null)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be a date';

        if ($this->format) {
            $this->message .= ' with format ' . $this->format;
        }
    }

    protected function doValidate()
    {
        return null !== ($this->format ? Helper::toDateFromFormat($this->format, $this->context->value) : Helper::toDate($this->context->value));
    }
}
