<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Context;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function testContext()
    {
        $context = new Context('foo.bar');
        $context->setValueType('file');

        $this->assertSame('file', $context->getValueType());
        $this->assertTrue($context->isValueType('file'));
        $this->assertFalse($context->isValueIgnored());
        $this->assertTrue($context->ignoreValue()->isValueIgnored());
        $this->assertFalse($context->isPropagationStopped());
        $this->assertTrue($context->stopPropagation()->isPropagationStopped());
    }
}
