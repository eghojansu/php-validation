<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Context;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function testContext()
    {
        $context = new Context('foo.bar');
        $context->type('file', true);

        $this->assertSame('file', $context->type());
        $this->assertTrue($context->type('file'));
        $this->assertFalse($context->isValueIgnored());
        $this->assertTrue($context->ignoreValue()->isValueIgnored());
        $this->assertFalse($context->isPropagationStopped());
        $this->assertTrue($context->stopPropagation()->isPropagationStopped());
        $this->assertSame('text', $context->withSelf(fn($context) => $context->add = 'text')->add);
    }
}
