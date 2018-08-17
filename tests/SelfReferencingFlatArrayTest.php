<?php
/* FlatArray | https://gitlab.com/byjoby/flatarray | MIT License */
declare(strict_types=1);
namespace FlatArray;

use PHPUnit\Framework\TestCase;

class SelfReferencingFlatArrayTest extends TestCase
{
    public function testVariables()
    {
        $f = new SelfReferencingFlatArray([
            'foo' => 'bar',
            'bar' => ['baz'=>'qux'],
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
    }
}
