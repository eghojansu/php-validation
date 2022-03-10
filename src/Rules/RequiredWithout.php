<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class RequiredWithout extends Rule
{
    public function __construct(string ...$fields)
    {
        parent::__construct(
            'This value should not empty',
            fn($value) => Arr::some(
                $fields,
                fn(string $field) => Val::isEmpty($this->result->other($field, $this->context->position)),
            ) && Val::isEmpty($value, false),
        );
    }
}
