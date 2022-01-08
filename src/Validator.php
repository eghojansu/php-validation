<?php

namespace Ekok\Validation;

use Ekok\Utils\Arr;
use Ekok\Utils\Str;
use Ekok\Utils\Val;
use Ekok\Utils\Payload;

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

    public function addRule(string|Rule $rule, string $name = null): static
    {
        if (is_string($rule) && !is_subclass_of($rule, Rule::class)) {
            throw new \LogicException(sprintf('Rule %s should be subclass of %s', $rule, Rule::class));
        }

        $this->rules[$name ?? $rule::name()] = $rule;

        return $this;
    }

    public function setRules(array $rules): static
    {
        array_walk($rules, fn($rule, $name) => $this->addRule($rule, is_numeric($name) ? null : $name));

        return $this;
    }

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    public function addNamespace(string ...$namespaces): static
    {
        array_push($this->namespaces, ...array_map(fn(string $ns) => rtrim($ns, '\\') . '\\', $namespaces));

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
            throw new ValidationException($result->getErrors());
        }

        return $result;
    }

    protected function doValidate(string $field, string|array $rules, Result $result, array $messages = null): void
    {
        /** @var Rule[] */
        $validators = $this->extract($rules);
        $run = function(Rule $validator, string $field, string|int $pos = null) use ($result, $messages) {
            $ctx = new Context($field, $result[$field], $pos);
            $val = $validator->validate($ctx, $result);

            if (false === $val) {
                $result->addError($field, $messages[$validator->name()] ?? $this->messages[$validator->name()] ?? $validator->getMessage());
            } elseif (!$ctx->isValueIgnored()) {
                $result[$field] = true === $val ? $result[$field] : $val;
            }

            return $ctx->isPropagationStopped();
        };
        $runBatch = static fn(Rule $validator, int $pos) => array_reduce(
            is_array($data = $result[$field]) ? array_keys($data) : array(),
            fn($stop, $key) => $stop || $run($validator, Helper::replaceWild($field, $pos, $key), $key),
        );

        foreach ($validators as $validator) {
            $stop = Helper::isWild($field, $pos) && $validator->isIterable() ? $runBatch($validator, $pos) : $run($validator, $field);

            if ($stop) {
                break;
            }
        }
    }

    protected function extract(string|array $rules): array
    {
        return Arr::each(
            is_string($rules) ? $this->parse($rules) : $rules,
            fn(Payload $param) => $param->value instanceof Rule ? $param->value : $this->findRule($param->key, (array) $param->value),
        );
    }

    protected function parse(string $rules): array
    {
        return Arr::each(
            explode('|', $rules),
            static function (Payload $rule) {
                list($name, $line) = explode(':', $rule->value . ':');

                return $rule->update(
                    array_map(Val::class . '::cast', array_filter(explode(',', $line), fn($arg) => '' !== $arg)),
                    $name,
                );
            },
        );
    }

    protected function findRule(string $rule, array $params): Rule
    {
        $class = $this->rules[$rule] ?? Arr::first(
            $this->namespaces,
            fn(Payload $ns) => class_exists($cls = $ns->value . $rule)
                || class_exists($cls = $ns->value . $rule . Rule::SUFFIX_NAME)
                || class_exists($cls = $ns->value . Str::casePascal($rule))
                || class_exists($cls = $ns->value . Str::casePascal($rule) . Rule::SUFFIX_NAME) ? $cls : null,
        );

        if (!$class || (is_object($class) && !$class instanceof Rule)) {
            throw new \LogicException(sprintf('Validation rule not found: %s', $rule));
        }

        if ($class instanceof Rule) {
            return $class->setParameters($params);
        }

        return new $class(...$params);
    }
}
