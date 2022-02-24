<?php

namespace Ekok\Validation\Rules;

use Ekok\Utils\Arr;
use Ekok\Utils\Val;
use Ekok\Validation\Rule;

class RequiredWithoutAll extends Rule
{
    protected $message = 'This value should not empty';
    private $fields = array();

    public function __construct(string ...$fields)
    {
        $this->fields = $fields;
    }

    protected function doValidate($value)
    {
        return Arr::every(
            $this->fields,
            fn(string $field) => Val::isEmpty($this->result->other($field, $this->context->position)),
        ) && Val::isEmpty($value, false);
    }
}
