<?php

namespace Ekok\Validation;

use Ekok\Utils\Arr;
use Ekok\Utils\Str;

class Rule
{
    const SUFFIX_NAME = 'Rule';

    /** @var bool */
    private $iterable = true;

    /** @var array */
    private $params = array();

    /** @var bool */
    private $definitionsDefined = false;

    /** @var array */
    private $definitions = array();

    /** @var string */
    private $message;

    /** @var callable|null */
    private $cb;

    /** @var bool */
    private $bind;

    /** @var Context */
    protected $context;

    /** @var Result */
    protected $result;

    public function __construct(
        string $message = null,
        callable $cb = null,
        bool $bind = null,
    ) {
        $this->message = $message ?? 'This value is not valid';
        $this->cb = $cb;
        $this->bind = $bind;
    }

    public static function name(): string
    {
        if (str_ends_with(static::class, static::SUFFIX_NAME)) {
            return Str::className(substr(static::class, 0, -strlen(static::SUFFIX_NAME)), true);
        }

        return Str::className(static::class, true);
    }

    public function validate(Context $context, Result $result)
    {
        $this->context = $context;
        $this->result = $result;

        $this->prepare();

        return $this->doValidate($this->context->value);
    }

    public function isIterable(): bool
    {
        return $this->iterable;
    }

    public function setIterable(bool $iterable): static
    {
        $this->iterable = $iterable;

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

    public function hasDefinitions(): bool
    {
        return !!$this->definitions;
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function addDefinition(string|int $name, string $type = null, bool $required = false, bool $variadic = false, $default = null, int $pos = null): static
    {
        $definition = compact('name', 'type', 'default', 'variadic', 'required', 'pos');

        if (null === $pos) {
            $definition['pos'] = $this->definitions ? array_reduce($this->definitions, fn(int $prev, array $def) => max($prev, $def['pos']), -1) + 1 : 0;
        }

        $this->definitions[$name] = $definition;

        return $this;
    }

    public function setDefinitions(array $definitions): static
    {
        $this->definitions = array();

        array_walk($definitions, fn(array $def, int|string $name) => $this->addDefinition(
            $def['name'] ?? $name,
            $def['type'] ?? null,
            $def['required'] ?? false,
            $def['variadic'] ?? false,
            $def['default'] ?? null,
            $def['pos'] ?? null,
        ));

        return $this;
    }

    public function getParameters(): array
    {
        return $this->params;
    }

    public function setParameters(array $params): static
    {
        if (!$this->definitionsDefined) {
            $this->defineParameters();
            $this->definitionsDefined = true;
        }

        if ($this->definitions) {
            $this->assignParameters($params);
        } else {
            $this->params = $params;
        }

        return $this;
    }

    protected function assignParameters(array $params): void
    {
        $this->params = array();

        foreach ($this->definitions as $name => $arg) {
            if (!Arr::exists($params, $arg['pos']) && $arg['required']) {
                throw new \LogicException(sprintf('Please specify parameter %s at position %s', $name, $arg['pos']));
            }

            $value = $arg['variadic'] ? array_slice($params, $arg['pos']) : ($params[$arg['pos']] ?? $arg['default']);

            if (
                $arg['type']
                && (
                    ($arg['variadic'] && Arr::some($value, fn($item) => $arg['type'] != gettype($item), $last))
                    || (!$arg['variadic'] && $arg['type'] != ($type = gettype($value)))
                )
            ) {
                throw new \LogicException(sprintf(
                    'Parameter %s should be type of %s but %s given',
                    $name,
                    $arg['type'],
                    $type ?? gettype($last[1]),
                ));
            }

            $this->params[$name] = $value;
        }
    }

    protected function defineParameters()
    {
        // define parameters here
    }

    protected function prepare()
    {
        // any preparation before validation
    }

    protected function doValidate($value)
    {
        if ($this->cb) {
            $cb = $this->cb;
            $params = array_values($this->params);

            if ($this->bind && $cb instanceof \Closure) {
                return $cb->call($this, $value, ...$params);
            }

            return $cb($value, ...$params);
        }

        return false;
    }
}
