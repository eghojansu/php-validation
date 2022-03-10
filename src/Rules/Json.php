<?php

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Json extends Rule
{
    private $props;

    public function __construct(private bool|null $transform = null, string ...$props)
    {
        $this->props = $props;
    }

    public function getMessage(): string
    {
        return 'This value should be a valid JSON string' . (
            $this->props ? ' which contains all of these properties ' . implode(', ', $this->props) : null
        );
    }

    protected function doValidate($value)
    {
        $json = json_decode($value, true);
        $true = null !== $json && (!$this->props || !array_diff($this->props, array_keys($json)));

        return $this->transform && $true ? $json : $true;
    }
}
