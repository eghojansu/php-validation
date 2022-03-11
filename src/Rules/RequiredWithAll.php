<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class RequiredWithAll extends Rule
{
    public function __construct(string ...$fields)
    {
        parent::__construct(
            'This value should not empty',
            fn($value) => Arr::every(
                $fields,
                fn(string $field) => Val::isEmpty($this->result->other($field, $this->context->position), false),
            ) && Val::isEmpty($value, false),
        );
    }
}
