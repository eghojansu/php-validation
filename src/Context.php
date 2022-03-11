<?php

declare(strict_types=1);

namespace Ekok\Validation;

use Ekok\Utils\Val;

class Context
{
    private $valueIgnored = false;
    private $propagationStopped = false;
    private $valueType;

    public function __construct(
        public string $field,
        public $value = null,
        public int|string|null $position = null,
    ) {}

    public function updateIf($pass, $value): bool
    {
        if ($passed = Val::isTrue($pass)) {
            $this->value = $value instanceof \Closure ? $value($this->value) : $value;
        }

        return $passed;
    }

    public function isValueIgnored(): bool
    {
        return $this->valueIgnored;
    }

    public function ignoreValue(): static
    {
        $this->valueIgnored = true;

        return $this;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): static
    {
        $this->propagationStopped = true;

        return $this;
    }

    public function getValueType(): string
    {
        return $this->valueType ?? ($this->valueType = gettype($this->value));
    }

    public function isValueType(string $type): bool
    {
        return $type === $this->getValueType();
    }

    public function setValueType(string|null $type): static
    {
        $this->valueType = $type;

        return $this;
    }
}
