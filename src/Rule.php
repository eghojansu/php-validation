<?php

namespace Ekok\Validation;

use Ekok\Utils\Str;

abstract class Rule
{
    const SUFFIX_NAME = 'Rule';

    protected $message = 'This value is not valid';

    /** @var string */
    protected $name;

    /** @var bool */
    protected $iterable = true;

    /** @var Context */
    protected $context;

    /** @var Result */
    protected $result;

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

    public function name(): string
    {
        if (!$this->name) {
            $this->name = Str::caseSnake(ltrim(strrchr('\\' . static::class, '\\'), '\\'));

            if (str_ends_with($this->name, self::SUFFIX_NAME)) {
                $this->name = substr($this->name, 0, -strlen(self::SUFFIX_NAME));
            }
        }

        return $this->name;
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

    protected function prepare()
    {
        // any preparation before validation
    }

    protected function doValidate($value)
    {
        return !!$value;
    }
}
