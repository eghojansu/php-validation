<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;

class Distinct extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should not have any duplicated values',
            static fn($value) => count(array_unique($value)) === count($value),
        );

        $this->setIterable(false);
    }
}
