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
        $this->assertEquals(['foo', 'bar'], $f->get());
        $this->assertEquals('bar', $f->pop(null));
        $this->assertEquals(['foo'], $f->get());
        $this->assertEquals('foo', $f->pop(null));
        $this->assertEquals([], $f->get());
    }

    public function testPushIndexCreation()
    {
        // pushing to a nonexistent index creates it as an array
        $f = new FlatArray();
        $f->push('a.b', 'c');
        $this->assertEquals(['c'], $f['a.b']);
        $this->assertEquals(['a' => ['b' => ['c']]], $f->get());
        // pushing to an existing non-array index does nothing
        $f = new FlatArray(['a' => 'b']);
        $f->push('a', 'c');
        $this->assertEquals(['a' => 'b'], $f->get());
        // poping off a non-array does nothing
        $this->assertNull($f->pop('a'));
    }

    public function testShiftUnshift()
    {
        $f = new FlatArray();
        $f->unshift(null, 'foo');
        $f->unshift(null, 'bar');
        $this->assertEquals(['bar', 'foo'], $f->get());
        $this->assertEquals('bar', $f->shift(null));
        $this->assertEquals(['foo'], $f->get());
        $this->assertEquals('foo', $f->shift(null));
        $this->assertEquals([], $f->get());
    }

    public function testUnshiftIndexCreation()
    {
        // unshifting to a nonexistent index creates it as an array
        $f = new FlatArray();
        $f->unshift('a.b', 'c');
        $this->assertEquals(['c'], $f['a.b']);
        $this->assertEquals(['a' => ['b' => ['c']]], $f->get());
        // unshifting to an existing non-array index does nothing
        $f = new FlatArray(['a' => 'b']);
        $f->unshift('a', 'c');
        $this->assertEquals(['a' => 'b'], $f->get());
        // shifting off a non-array does nothing
        $this->assertNull($f->shift('a'));
    }
}
