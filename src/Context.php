<?php

namespace Ekok\Validation;

class Context
{
    protected $valueIgnored = false;
    protected $propagationStopped = false;
    protected $valueType;

    public function __construct(
        public string $field,
        public $value = null,
        public int|string|null $position = null,
    ) {}

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
