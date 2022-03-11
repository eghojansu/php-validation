<?php

declare(strict_types=1);

namespace Ekok\Validation;

use Ekok\Utils\Arr;
use Ekok\Utils\Str;
use Ekok\Utils\Val;

class Validator
{
    const RULE_NAMESPACE = 'Ekok\\Validation\\Rules\\';

    /** @var DynamicRule[] */
    private $rules = array();
    private $messages = array();
    private $namespaces = array(self::RULE_NAMESPACE);
    private $throwIfError = true;

    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRule(string|Rule $rule, string $name = null): static
    {
        if (is_string($rule) && !is_subclass_of($rule, Rule::class)) {
            throw new \LogicException(sprintf('Rule %s should be subclass of %s', $rule, Rule::class));
        }

        $add = $name ?? $rule::name();

        if (!$add) {
            throw new \LogicException('Rule should have a name');
        }

        $this->rules[$add] = $rule;

        return $this;
    }

    public function addCustomRule(string $name, \Closure $rule, string $message = null): static
    {
        $this->rules[$name] = new Rules\Callback($rule, $message);

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
            throw new ValidationException(null, $result);
        }

        return $result;
    }

    private function doValidate(string $field, string|array $rules, Result $result, array $messages = null): void
    {
        /** @var Rule[] */
        $validators = $this->extract($rules);
        $run = function(Rule $validator, string $field, string|int $pos = null) use ($result, $messages) {
            $ctx = new Context($field, $result[$field], $pos);
            $val = $validator->validate($ctx, $result);

            if (false === $val) {
                $ctx->stopPropagation();
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

    private function extract(string|array $rules): array
    {
        return Arr::each(
            is_string($rules) ? $this->parse($rules) : $rules,
            fn($param, $key) => $param instanceof Rule ? $param : $this->findRule($key, (array) $param),
        );
    }

    private function parse(string $rules): array
    {
        return Arr::reduce(
            explode('|', $rules),
            static function (array $rules, string $rule) {
                list($name, $line) = explode(':', $rule . ':');

                return $rules + array(
                    $name => array_map(
                        array(Val::class, 'cast'),
                        array_filter(explode(',', $line), fn ($arg) => '' !== $arg),
                    ),
                );
            },
            array(),
        );
    }

    private function findRule(string $rule, array $params): Rule
    {
        $class = $this->rules[$rule] ?? Arr::first(
            $this->namespaces,
            fn(string $ns) => class_exists($cls = $ns . $rule)
                || class_exists($cls = $ns . $rule . Rule::SUFFIX_NAME)
                || class_exists($cls = $ns . Str::casePascal($rule))
                || class_exists($cls = $ns . Str::casePascal($rule) . Rule::SUFFIX_NAME) ? $cls : null,
        );

        if (!$class || (is_object($class) && !$class instanceof Rule)) {
            throw new \LogicException(sprintf('Validation rule not found: %s', $rule));
        }

        return $class instanceof Rule ? $class->setParameters($params) : new $class(...$params);
    }
}
