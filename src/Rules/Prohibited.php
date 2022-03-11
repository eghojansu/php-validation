<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class Prohibited extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be empty or not present',
            fn($value) => !isset($this->result[$this->context->field]) || Val::isEmpty($value),
        );
    }
}
