<?php

namespace Ekok\Validation;

class Validator
{
    protected $rules = array();
    protected $messages = array();
    protected $throwIfError = true;

    public function __construct(array $rules = null, array $messages = null)
    {
        if ($rules) {
            $this->setRules($rules);
        }

        if ($rules) {
            $this->setMessages($messages);
        }
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRule(string $name, string|Rule $rule): static
    {
        $this->rules[$name] = $rule;

        return $this;
    }

    public function setRules(array $rules): static
    {
        array_walk($rules, fn($rule, $name) => $this->addRule($name, $rule));

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
        /** @var Rule[] */
        $validators = $this->extract($rules);

        foreach ($validators as $validator) {
            $value = $validator->validate($field, $result);

            if (true === $value) {
                $result[$field] = $result[$field];
            } elseif (false === $value) {
                $result->addError($field, $messages[$validator->name] ?? $validator->getMessage());
            } else {
                $result[$field] = $value;
            }
        }
    }

    protected function extract(string|array $rules): array
    {
        $validators = array();

        foreach (is_string($rules) ? $this->parse($rules) : $rules as $rule => $args) {
            $validators[] = $args instanceof Rule ? $args : $this->buildRule($rule, $args);
        }

        return $validators;
    }

    protected function parse(string $rules): array
    {
        return array_reduce(explode('|', $rules), function (array $prev, string $rule) {
            list($name, $args) = explode(':', $rule . ':');

            return $prev + array($name => array_map('Ekok\\Validation\\Helper::cast', explode(',', $args)));
        }, array());
    }

    protected function buildRule(string|int $rule, array $args): Rule
    {
        $found = $this->rules[$rule] ?? (class_exists($class = 'Ekok\\Validation\\Rules\\' . $rule) ? $class : null);

        if (!$found) {
            throw new \LogicException(sprintf('Invalid validation rules: %s', $rule));
        }

        /** @var Rule */
        $validator = is_string($found) ? new $found() : clone $found;

        return $validator->setArgs($args);
    }
}
