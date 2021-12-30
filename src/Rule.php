<?php

namespace Ekok\Validation;

abstract class Rule
{
    protected $args = array();
    protected $message = 'This value is not valid';

    abstract public function validate(string $field, Result $result);

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setArgs(array $args): static
    {
        $this->args = $args;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
