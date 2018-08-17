<?php
/* FlatArray | https://gitlab.com/byjoby/flatarray | MIT License */
declare(strict_types=1);
namespace FlatArray;

use PHPUnit\Framework\TestCase;

class FlatArrayPushPopTest extends TestCase
{
    public function testPushPop()
    {
        $f = new FlatArray();
        $f->push(null, 'foo');
        $f->push(null, 'bar');
        $this->assertEquals(['foo','bar'], $f->get());
        $this->assertEquals('bar', $f->pop(null));
        $this->assertEquals(['foo'], $f->get());
        $this->assertEquals('foo', $f->pop(null));
        $this->assertEquals([], $f->get());
    }

    public function testShiftUnshift()
    {
        $f = new FlatArray();
        $f->shift(null, 'foo');
        $f->shift(null, 'bar');
        $this->assertEquals(['bar','foo'], $f->get());
        $this->assertEquals('bar', $f->unshift(null));
        $this->assertEquals(['foo'], $f->get());
        $this->assertEquals('foo', $f->unshift(null));
        $this->assertEquals([], $f->get());
    }
}
