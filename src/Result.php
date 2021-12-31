<?php

namespace Ekok\Validation;

class Result implements \ArrayAccess
{
    protected $errors = array();
    protected $original = array();
    protected $data = array();

    public function __construct(array $original = null)
    {
        if ($original) {
            $this->original = $original;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return (
            Helper::ref($offset, $this->data, false, $exists) || $exists
            || Helper::ref($offset, $this->original, false, $exists) || $exists
        );
    }

    public function offsetGet(mixed $offset): mixed
    {
        $var = Helper::ref($offset, $this->data, false, $exists);

        if ($exists) {
            return $var;
        }

        return $this->original($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $var = Helper::ref($offset, $this->data, true);
        $var[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (false === $pos = strrpos($offset, '.')) {
            unset($this->data[$offset]);

            return;
        }

        $root = substr($offset, 0, $pos);
        $leaf = substr($offset, $pos + 1);
        $var = &Helper::ref($root, $this->data, true);

        if (is_array($var) || $var instanceof \ArrayAccess) {
            unset($var[$leaf]);
        } elseif (is_object($var) && is_callable($remove = array($var, 'remove' . $leaf))) {
            $remove();
        } elseif (is_object($var) && isset($var->$leaf)) {
            unset($var->$leaf);
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getOriginal(): array
    {
        return $this->original;
    }

    public function other(string $field, string|int|null $key)
    {
        $fetch = Helper::isWild($field, $pos) && $key ? Helper::replaceWild($field, $pos, $key) : $field;

        return $this[$fetch];
    }

    public function original(string $field)
    {
        return Helper::ref($field, $this->original);
    }

    public function success(): bool
    {
        return !$this->errors;
    }

    public function failed(): bool
    {
        return !$this->success();
    }

    public function error(string $field, bool $flatten = true, string $glue = ','): string|array
    {
        return $flatten ? implode($glue, $this->errors[$field] ?? array()) : $this->errors[$field] ?? array();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(string $field, string $message): static
    {
        $this->errors[$field][] = $message;

        return $this;
    }

    public function setError(string $field, array $messages): static
    {
        $this->errors[$field] = array_values($messages);

        return $this;
    }

    public function setErrors(array $errors): static
    {
        $this->errors = array();

        array_walk($errors, fn($errors, $field) => $this->setError($field, $errors));

        return $this;
    }
}
