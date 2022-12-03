<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */
declare(strict_types=1);
namespace Flatrr;

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
        $f->unshift(null, 'foo');
        $f->unshift(null, 'bar');
        $this->assertEquals(['bar','foo'], $f->get());
        $this->assertEquals('bar', $f->shift(null));
        $this->assertEquals(['foo'], $f->get());
        $this->assertEquals('foo', $f->shift(null));
        $this->assertEquals([], $f->get());
    }
}
