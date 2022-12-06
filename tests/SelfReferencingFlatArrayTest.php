<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

declare(strict_types=1);

namespace Flatrr;

use PHPUnit\Framework\TestCase;

class SelfReferencingFlatArrayTest extends TestCase
{
    public function testVariables()
    {
        $f = new SelfReferencingFlatArray([
            'foo' => 'bar',
            'bar' => ['baz' => 'qux'],
            'test' => [
                'foo' => '${foo}',
                'bar' => '${bar.baz}',
                'none' => '${foo.none}',
                'escaped' => [
                    'full' => '$\\{foo\\}',
                    'left' => '$\\{foo}',
                    'right' => '${foo\\}'
                ],
            ],
            'nested' => [
                'foo' => '${test.foo}',
                'bar' => '${test.bar}',
                'none' => '${test.none}',
                'escaped' => [
                    'full' => '${test.escaped.full}',
                    'left' => '${test.escaped.left}',
                    'right' => '${test.escaped.right}'
                ]
            ]
        ]);
        //basic references
        $this->assertEquals('bar', $f['test.foo']);
        $this->assertEquals('qux', $f['test.bar']);
        $this->assertEquals('${foo.none}', $f['test.none']);
        //nested references
        $this->assertEquals('bar', $f['nested.foo']);
        $this->assertEquals('qux', $f['nested.bar']);
        $this->assertEquals('${foo.none}', $f['nested.none']);
        //escaped
        $this->assertEquals('${foo}', $f['test.escaped.full']);
        $this->assertEquals('${foo}', $f['test.escaped.left']);
        $this->assertEquals('${foo}', $f['test.escaped.right']);
        //nested and escaped
        $this->assertEquals('${foo}', $f['nested.escaped.full']);
        $this->assertEquals('${foo}', $f['nested.escaped.left']);
        $this->assertEquals('${foo}', $f['nested.escaped.right']);
        //raw
        $this->assertEquals('${foo}', $f->get('test.foo', true));
    }

    public function testSettingFalseyValues()
    {
        $a = new SelfReferencingFlatArray(['foo' => ['bar' => 'baz']]);
        $a['foo.bar'] = false;
        $this->assertFalse($a['foo.bar']);
        $a['foo.bar'] = 0;
        $this->assertSame(0, $a['foo.bar']);
        $a['foo.bar'] = '';
        $this->assertSame('', $a['foo.bar']);
        $a['foo.bar'] = [];
        $this->assertSame([], $a['foo.bar']);
    }

    public function testMergingFalseyValues()
    {
        $a = new SelfReferencingFlatArray(['foo' => ['bar' => 'baz']]);
        $a->merge(['foo' => ['bar' => false]], null, true);
        $this->assertFalse($a['foo.bar']);
        $a->merge(['foo' => ['bar' => 0]], null, true);
        $this->assertSame(0, $a['foo.bar']);
        $a->merge(['foo' => ['bar' => '']], null, true);
        $this->assertSame('', $a['foo.bar']);
        $a->merge(['foo' => ['bar' => []]], null, true);
        $this->assertSame([], $a['foo.bar']);
    }

    public function testForeach()
    {
        $reference = [
            'a' => 'b',
            'b' => '${a}',
            'd' => '${b}'
        ];
        $arr = new SelfReferencingFlatArray($reference);
        foreach ($arr as $key => $value) {
            $this->assertEquals('b', $value);
        }
    }

    public function testPop()
    {
        $f = new SelfReferencingFlatArray([
            'a' => 'b',
            'c' => [
                '${a}'
            ]
        ]);
        $this->assertEquals('b', $f->pop('c'));
        $this->assertNull($f->pop('c'));
    }

    public function testShift()
    {
        $f = new SelfReferencingFlatArray([
            'a' => 'b',
            'c' => [
                '${a}'
            ]
        ]);
        $this->assertEquals('b', $f->shift('c'));
        $this->assertNull($f->shift('c'));
    }
}
