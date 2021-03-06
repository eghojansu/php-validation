<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class RequiredIf extends Rule
{
    public function __construct(string $field, $value)
    {
        parent::__construct(
            'This value should not empty',
            fn($val) => $value == $this->result->other($field, $this->context->position) && Val::isEmpty($val, false),
        );
    }
}
