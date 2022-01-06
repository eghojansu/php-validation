<?php

namespace Ekok\Validation;

class Context
{
    protected $valueIgnored = false;
    protected $propagationStopped = false;
    protected $type;

    public function __construct(
        public string $field,
        public $value = null,
        public int|string|null $position = null,
    ) {}

    public function type(string $type = null, bool $set = false): string|bool
    {
        if ($type) {
            if ($set) {
                $this->type = $type;
            }

            return $type === ($this->type ?? ($this->type = gettype($this->value)));
        }

        return $this->type ?? ($this->type = gettype($this->value));
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

    public function withSelf(callable $cb): static
    {
        $cb($this);

        return $this;
    }
}
