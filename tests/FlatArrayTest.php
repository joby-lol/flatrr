<?php
/* FlatArray | https://gitlab.com/byjoby/flatarray | MIT License */
declare(strict_types=1);
namespace FlatArray;

use PHPUnit\Framework\TestCase;

class FlatArrayTest extends TestCase
{
    public function testGetting()
    {
        $data = [
            'a' => 'A',
            'b' => ['c'=>'C']
        ];
        $a = new FlatArray($data);
        //first level
        $this->assertEquals('A', $a['a']);
        $this->assertEquals('A', $a->get('a'));
        //nested
        $this->assertEquals('C', $a['b.c']);
        $this->assertEquals('C', $a->get('b.c'));
        //returning array
        $this->assertEquals(['c'=>'C'], $a['b']);
        $this->assertEquals(['c'=>'C'], $a->get('b'));
        //returning entire array by requesting null or empty string
        $this->assertEquals($data, $a[null]);
        $this->assertEquals($data, $a->get());
        $this->assertEquals($data, $a['']);
        $this->assertEquals($data, $a->get(''));
        //requesting invalid keys should return null
        $this->assertNull($a->get('nonexistent'));
        $this->assertNull($a->get('b.nonexistent'));
        $this->assertNull($a->get('..'));
        $this->assertNull($a->get('.'));
        //double dots
        $this->assertNull($a->get('..a'));
        $this->assertNull($a->get('a..'));
        $this->assertNull($a->get('..a..'));
        $this->assertNull($a->get('..a..'));
        $this->assertNull($a->get('b..c'));
        $this->assertNull($a->get('b..c..'));
        $this->assertNull($a->get('..b..c'));
        $this->assertNull($a->get('..b..c..'));
        $this->assertNull($a->get('b.c..'));
        $this->assertNull($a->get('..b.c'));
        $this->assertNull($a->get('..b.c..'));
        //single dots
        $this->assertNull($a->get('.a'));
        $this->assertNull($a->get('a.'));
        $this->assertNull($a->get('.a.'));
        $this->assertNull($a->get('.a.'));
        $this->assertNull($a->get('b.c.'));
        $this->assertNull($a->get('.b.c'));
        $this->assertNull($a->get('.b.c.'));
        $this->assertNull($a->get('b.c.'));
        $this->assertNull($a->get('.b.c'));
        $this->assertNull($a->get('.b.c.'));
    }

    public function testSetting()
    {
        $data = [
            'a' => 'A',
            'b' => ['c'=>'C']
        ];
        $a = new FlatArray($data);
        //setting on first layer
        $a['a'] = 'B';
        $this->assertEquals('B', $a['a']);
        $a['new'] = 'NEW';
        $this->assertEquals('NEW', $a['new']);
        //setting nested
        $a['b.c'] = 'D';
        $this->assertEquals('D', $a['b.c']);
        $a['b.new'] = 'NEW';
        $this->assertEquals('NEW', $a['b.new']);
        //final state
        $this->assertEquals(
            [
                'a' => 'B',
                'b' => [
                    'c' => 'D',
                    'new' => 'NEW'
                ],
                'new' => 'NEW'
            ],
            $a->get()
        );
    }

    public function testCaseNormalizing()
    {
        $h = new FlatArray([
            'ABC'=>['ABC'=>'ABC']
        ]);
        $this->assertEquals('ABC', $h['abc.abc']);
        $this->assertEquals('ABC', $h['Abc.aBC']);
        $h['Abc.aBC'] = 'abc';
        $this->assertEquals('abc', $h['abc.abc']);
        $this->assertEquals('abc', $h['Abc.aBC']);
        $h['ABC'] = ['ABC'=>'ABC'];
        $this->assertEquals('ABC', $h['abc.abc']);
        $this->assertEquals('ABC', $h['Abc.aBC']);
    }

    public function testAccidentalSubstrings()
    {
        $h = new FlatArray(['foo'=>'bar']);
        $this->assertNull($h['foo.baz']);
    }

    public function testMerge()
    {
        $data = [
            'a' => 'b',
            'c' => [
                'd' => 'e'
            ]
        ];
        //overwrite false, original values should be preserved
        $c = new FlatArray($data);
        $c->merge([
            'a' => 'B',
            'c' => [
                'd' => 'E',
                'f' => 'g'
            ],
            'h' => 'i'
        ]);
        $this->assertEquals('b', $c['a']);
        $this->assertEquals('e', $c['c.d']);
        $this->assertEquals('i', $c['h']);
        $this->assertEquals('g', $c['c.f']);
        //overwrite true, original values should be overwritten
        $c = new FlatArray($data);
        $c->merge([
            'a' => 'B',
            'c' => [
                'd' => 'E',
                'f' => 'g'
            ],
            'h' => 'i'
        ], null, true);
        $this->assertEquals('B', $c['a']);
        $this->assertEquals('E', $c['c.d']);
        $this->assertEquals('i', $c['h']);
        $this->assertEquals('g', $c['c.f']);
        //overwrite false with mismatched array-ness
        $c = new FlatArray($data);
        $c->merge([
            'a' => ['b'=>'c'],
            'c' => 'd'
        ]);
        $this->assertEquals('b', $c['a']);
        $this->assertEquals('e', $c['c.d']);
        //overwrite true with mismatched array-ness
        $c = new FlatArray($data);
        $c->merge([
            'a' => ['b'=>'c'],
            'c' => 'd'
        ], null, true);
        $this->assertEquals('c', $c['a.b']);
        $this->assertEquals('d', $c['c']);
    }

    public function testConstructionUnflattening()
    {
        $arr = new FlatArray([
            'foo.bar' => 'baz'
        ]);
        $this->assertEquals(
            ['foo'=>['bar'=>'baz']],
            $arr->get()
        );
    }
}
