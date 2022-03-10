<?php

namespace Ekok\Validation;

use Ekok\Utils\Val;

class Result implements \ArrayAccess
{
    private $errors = array();
    private $original = array();
    private $data = array();

    public function __construct(array $original = null)
    {
        if ($original) {
            $this->original = $original;
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

    public function success(): bool
    {
        return !$this->errors;
    }

    public function failed(): bool
    {
        return !$this->success();
    }

    public function error(string $field, bool $flatten = true, string $glue = ', '): string|array
    {
        return $flatten ? implode($glue, $this->errors[$field] ?? array()) : $this->errors[$field] ?? array();
    }

    public function getErrors(bool $plain = false, string $glue = ', '): array
    {
        return $plain ? array_map(fn($errors) => implode($glue, $errors), $this->errors) : $this->errors;
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

    public function original(string $field)
    {
        return $this->ref($field, $this->original);
    }

    public function other(string $field, string|int|null $key)
    {
        $fetch = Helper::isWild($field, $pos) && $key ? Helper::replaceWild($field, $pos, $key) : $field;

        return $this[$fetch];
    }

    public function &ref(string $key, array &$ref, bool $add = false, bool &$exists = null)
    {
        if (substr_count($key, '*') > 1) {
            throw new \LogicException('Unsupported key with multiple wildcard symbol');
        }

        $pos = strpos($key, '*');

        if (false === $pos) {
            $val = &Val::ref($key, $ref, $add, $exists);

            return $val;
        }

        $root = substr($key, 0, $pos - 1);
        $name = substr($key, $pos + 2);
        $val = &Val::ref($root, $ref, $add, $exists);

        if (!$name || !$exists || !is_array($val)) {
            return $val;
        }

        $pos = strpos($name, '.');
        $value = false === $pos ? array_column($val, $name) : array_map(fn(array $row) => Val::ref($name, $row), $val);

        return $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return (
            $this->ref($offset, $this->data, false, $exists)
            || $exists
            || $this->ref($offset, $this->original, false, $exists)
            || $exists
        );
    }

    public function offsetGet(mixed $offset): mixed
    {
        $var = $this->ref($offset, $this->data, false, $exists);

        if ($exists) {
            return $var;
        }

        return $this->original($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $var = &$this->ref($offset, $this->data, true);
        $var = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        Val::unref($offset, $this->data);
    }
}
