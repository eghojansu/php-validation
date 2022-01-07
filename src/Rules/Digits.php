<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Digits extends Rule
{
    protected $message = 'This value should be entirely digits characters';

    public function __construct(private int|null $min = null, private int|null $max = null)
    {}

    protected function prepare()
    {
        if ($this->max && $this->min) {
            $this->message .= ' with length between ' . $this->min . ' and ' . $this->max . ' characters';
        } elseif ($this->min) {
            $this->message .= ' within ' . $this->min . ' characters';
        }
    }

    protected function doValidate($value)
    {
        return preg_match('/^[[:digit:]]+$/', $value) && (null === $this->min
            || ($len = strlen($value)) === $this->min
            || ($this->max && ($len >= $this->min && $len <= $this->max))
        );
    }
}
