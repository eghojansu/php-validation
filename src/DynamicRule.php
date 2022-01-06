<?php

namespace Ekok\Validation;

interface DynamicRule
{
    public function setArguments(array $arguments): Rule;
}
