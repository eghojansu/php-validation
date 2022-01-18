<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testToBool()
    {
        $this->assertTrue(Helper::toBool('true'));
        $this->assertTrue(Helper::toBool('1'));
        $this->assertFalse(Helper::toBool('off'));
        $this->assertFalse(Helper::toBool(0));
        $this->assertTrue(Helper::toBool('on'));
    }

    public function testToDate()
    {
        $format = 'Y-m-d';
        $today = date($format);

        $this->assertSame($today, Helper::toDate('today')->format($format));
        $this->assertNull(Helper::toDate('invalid date'));
    }

    public function testToDateFromFormat()
    {
        $format = 'd-m-Y';
        $today = date($format);

        $this->assertSame($today, Helper::toDateFromFormat($format, $today)->format($format));
        $this->assertNull(Helper::toDateFromFormat('invalid date', $format));
    }

    public function testToSize()
    {
        $this->assertSame(20, Helper::toSize(20));
        $this->assertSame(20.2, Helper::toSize(20.2));
        $this->assertSame(4, Helper::toSize(20.2, 'string'));
        $this->assertSame(3, Helper::toSize('foo'));
        $this->assertSame(3, Helper::toSize(range(1,3)));
        $this->assertSame(0.5, Helper::toSize(array('size' => 512), 'file'));
        $this->assertSame(0, Helper::toSize(new \stdClass()));
        $this->assertSame(117, Helper::toSize(new class implements \Countable {
            public function count(): int
            {
                return 117;
            }
        }));
    }

    public function testToSizeGreater()
    {
        $this->assertSame('high', Helper::toSizeGreater(20));
        $this->assertSame('high', Helper::toSizeGreater(20.2));
        $this->assertSame('long', Helper::toSizeGreater(20.2, 'string'));
        $this->assertSame('long', Helper::toSizeGreater('foo'));
        $this->assertSame('much', Helper::toSizeGreater(range(1,3)));
        $this->assertSame('big', Helper::toSizeGreater(array('size' => 512), 'file'));
        $this->assertSame('high', Helper::toSizeGreater(new \stdClass()));
        $this->assertSame('high', Helper::toSizeGreater(new class implements \Countable {
            public function count(): int
            {
                return 117;
            }
        }));
    }

    public function testToSizeLower()
    {
        $this->assertSame('low', Helper::toSizeLower(20));
        $this->assertSame('low', Helper::toSizeLower(20.2));
        $this->assertSame('short', Helper::toSizeLower(20.2, 'string'));
        $this->assertSame('short', Helper::toSizeLower('foo'));
        $this->assertSame('few', Helper::toSizeLower(range(1,3)));
        $this->assertSame('small', Helper::toSizeLower(array('size' => 512), 'file'));
        $this->assertSame('low', Helper::toSizeLower(new \stdClass()));
        $this->assertSame('low', Helper::toSizeLower(new class implements \Countable {
            public function count(): int
            {
                return 117;
            }
        }));
    }

    public function testIsWild()
    {
        $this->assertTrue(Helper::isWild('foo.*.bar'));
        $this->assertFalse(Helper::isWild('foo.bar'));
    }

    public function testReplaceWild()
    {
        $this->assertSame('foo.1.bar', Helper::replaceWild('foo.*.bar', 4, 1));
    }
}
