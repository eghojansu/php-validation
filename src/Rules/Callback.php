<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Callback extends Rule
{
    public function __construct(\Closure $cb, string $message = null)
    {
        parent::__construct($message, $cb, true);
    }
}
