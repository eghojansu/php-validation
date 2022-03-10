<?php

use Ekok\Validation\Context;

class ContextTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testFunctionality()
    {
        $context = new Context('foo.bar');
        $context->setValueType('file');

        $this->assertSame('file', $context->getValueType());
        $this->assertTrue($context->isValueType('file'));
        $this->assertFalse($context->isValueIgnored());
        $this->assertTrue($context->ignoreValue()->isValueIgnored());
        $this->assertFalse($context->isPropagationStopped());
        $this->assertTrue($context->stopPropagation()->isPropagationStopped());

        $this->assertNull($context->value);

        $this->assertFalse($context->updateIf(static fn() => false, 'foo'));
        $this->assertNull($context->value);

        $this->assertTrue($context->updateIf(true, 'foo'));
        $this->assertSame('foo', $context->value);

        $this->assertTrue($context->updateIf(static fn() => true, static fn($old) => $old . 'bar'));
        $this->assertSame('foobar', $context->value);
    }
}
