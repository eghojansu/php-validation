<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class After extends Rule
{
    public function __construct(private string $date, private bool $equal = false)
    {
        $this->message = 'This value should be after ';

        if ($equal) {
            $this->message .= 'or equal to ';
        }

        $this->message .= $this->date;
    }

    protected function doValidate()
    {
        return (
            ($a = Helper::toDate($this->context->value))
            && ($b = Helper::toDate($this->result->other($this->date, $this->context->position)) ?? Helper::toDate($this->date))
            && ($this->equal ? $a >= $b : $a > $b)
        );
    }
}
