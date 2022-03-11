<?php

declare(strict_types=1);

namespace Ekok\Validation\Rules;

use Ekok\Validation\Rule;
use Ekok\Validation\Helper;

class Boolean extends Rule
{
    public function __construct()
    {
        parent::__construct(
            'This value should be able to cast as boolean',
            fn($value) => $this->context->updateIf(
                null !== ($update = Helper::toBool($value)),
                $update,
            ),
        );
    }
}
