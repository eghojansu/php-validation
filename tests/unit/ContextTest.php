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
    }
}
