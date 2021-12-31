<?php

namespace Ekok\Validation;

class Validator
{
    const RULE_NAMESPACE = 'Ekok\\Validation\\Rules\\';

    /** @var DynamicRule[] */
    protected $rules = array();
    protected $messages = array();
    protected $namespaces = array(self::RULE_NAMESPACE);
    protected $throwIfError = true;

    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRule(string $name, DynamicRule $rule): static
    {
        $this->rules[$name] = $rule;

        return $this;
    }

    public function setRules(array $rules): static
    {
        array_walk($rules, fn($rule, $name) => $this->addRule($name, $rule));

        return $this;
    }

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    public function addNamespace(string $namespace): static
    {
        $this->namespaces[] = $namespace;

        return $this;
    }

    public function setNamespaces(array $namespaces): static
    {
        $this->namespaces = array(self::RULE_NAMESPACE);

        array_walk($namespaces, fn($namespace) => $this->addNamespace($namespace));

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function addMessage(string $rule, string $message): static
    {
        $this->messages[$rule] = $message;

        return $this;
    }

    public function setMessages(array $messages): static
    {
        array_walk($messages, fn($message, $name) => $this->addMessage($name, $message));

        return $this;
    }

    public function isThrowIfError(): bool
    {
        return $this->throwIfError;
    }

    public function setThrowIfError(bool $throwIfError): static
    {
        $this->throwIfError = $throwIfError;

        return $this;
    }

    public function validate(array $rules, array $data, array $messages = null): Result
    {
        $result = new Result($data);

        array_walk($rules, fn($rule, $field) => $this->doValidate($field, $rule, $result, $messages));

        if ($result->failed() && $this->throwIfError) {
            throw new Exception($result->getErrors());
        }

        return $result;
    }

    protected function doValidate(string $field, string|array $rules, Result $result, array $messages = null): void
    {
        $run = static function(Rule $validator, string $field, string|int $pos = null) use ($result, $messages) {
            $ctx = new Context($field, $result[$field], $pos);
            $val = $validator->validate($ctx, $result);

            if (false === $val) {
                $result->addError($field, $messages[$validator->name()] ?? $validator->getMessage());
            } elseif (!$ctx->isValueIgnored()) {
                $result[$field] = true === $val ? $result[$field] : $val;
            }

            return $ctx->isPropagationStopped();
        };
        $runBatch = static fn(Rule $validator, int $pos) => array_reduce(
            is_array($data = $result[substr($field, 0, $pos - 1)]) ? array_keys($data) : array(),
            fn($stop, $key) => $stop || $run($validator, Helper::replaceWild($field, $pos, $key), $key),
        );

        foreach ($this->extract($rules) as $validator) {
            $stop = Helper::isWild($field, $pos) ? $runBatch($validator, $pos) : $run($validator, $field);

            if ($stop) {
                break;
            }
        }
    }

    protected function extract(string|array $rules): array
    {
        return Helper::each(
            is_string($rules) ? $this->parse($rules) : $rules,
            fn($args, $rule) => $args instanceof Rule ? $args : $this->findRule($rule, $args),
        );
    }

    protected function parse(string $rules): array
    {
        return Helper::each(
            explode('|', $rules),
            static function (string $rule, ...$args) {
                list($name, $line) = explode(':', $rule . ':');

                $args[1]['seed']->key = $name;

                return array_map('Ekok\\Validation\\Helper::cast', explode(',', $line));
            },
        );
    }

    protected function findRule(string|int $rule, array $args): Rule
    {
        if (isset($this->rules[$rule])) {
            return (clone $this->rules[$rule])->setArguments($args);
        }

        $class = array_reduce(
            $this->namespaces,
            fn(string|null $found, $namespace) => $found ?? (class_exists($class = $namespace . $rule) || class_exists($class = $namespace . $rule . Rule::SUFFIX_NAME) ? $class : null),
        );

        if ($class) {
            return new $class(...$args);
        }

        throw new \LogicException(sprintf('Validation rule not found: %s', $rule));
    }
}
