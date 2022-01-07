<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Trim extends Rule
{
    protected $message = 'This value should be a string';

    public function __construct(
        private string|null $chars = null,
        private bool $left = true,
        private bool $right = true
    ) {}

    protected function doValidate($value)
    {
        $chars = $this->chars ?? " \t\n\r\0\x0B";
        $trim = $this->left && $this->right ? 'trim' : ($this->left ? 'ltrim' : 'rtrim');

        return $this->context->type('string') ? $trim($value, $chars) : false;
    }
}