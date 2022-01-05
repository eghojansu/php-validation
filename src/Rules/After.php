<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Helper;
use Ekok\Validation\Rule;

class After extends Rule
{
    public function __construct(private string $date, private bool $equals = false)
    {}

    protected function prepare()
    {
        $this->message = 'This value should be after ';

        if ($this->equals) {
            $this->message .= 'or equals to ';
        }

        $this->message .= $this->date;
    }

    protected function doValidate($value)
    {
        return (
            ($a = Helper::toDate($value))
            && ($b = Helper::toDate($this->result->other($this->date, $this->context->position)) ?? Helper::toDate($this->date))
            && ($this->equals ? $a >= $b : $a > $b)
        );
    }
}
