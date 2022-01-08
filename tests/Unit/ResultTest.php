<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testResult()
    {
        $origin = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'user' => array(
                'name' => 'Argon',
            ),
            'options' => array(
                array('name' => 'one', 'count' => 1, 'options' => array('value' => 'one')),
                array('name' => 'two', 'count' => 2, 'options' => array('value' => 'two')),
            ),
        );
        $result = new Result($origin);

        $this->assertSame(array(), $result->getData());
        $this->assertSame($origin, $result->getOriginal());
        $this->assertTrue($result->success());
        $this->assertFalse($result->failed());
        $this->assertSame('', $result->error('foo'));
        $this->assertSame('bar', $result->original('foo'));
        $this->assertCount(0, $result->getErrors());
        $this->assertCount(0, $result->getErrors(false));
        $this->assertSame('message', $result->addError('foo', 'message')->error('foo'));
        $this->assertSame('update', $result->setError('foo', array('update'))->error('foo'));
        $this->assertSame('new error', $result->setErrors(array('foo' => array('new error')))->error('foo'));

        // accessing
        $this->assertArrayHasKey('foo', $result);
        $this->assertTrue(isset($result['foo']));
        $this->assertSame('bar', $result['foo']);

        $result['foo'] = 'update';

        $this->assertSame('update', $result['foo']);
        $this->assertSame('bar', $result->original('foo'));

        unset($result['foo']);

        $this->assertSame('bar', $result['foo']);
        $this->assertSame('Argon', $result['user.name']);
        $this->assertSame(array_column($origin['options'], 'name'), $result['options.*.name']);
        $this->assertSame(array_column($origin['options'], 'count'), $result['options.*.count']);
        $this->assertSame(array('one', 'two'), $result['options.*.options.value']);

        $this->expectExceptionMessage('Unsupported key with multiple wildcard symbol');

        $x = $result['unknown.*.data.*.foo'];
    }
}
