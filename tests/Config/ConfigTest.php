<?php
/* Flatrr | https://gitlab.com/byjoby/flatrr | MIT License */
declare(strict_types=1);
namespace Flatrr\Config;

use PHPUnit\Framework\TestCase;
use Spyc;

class ConfigTest extends TestCase
{
    public function testVariables()
    {
        //single level of variable
        $c = new Config();
        $c['a.b'] = 'a';
        $c['c'] = '${a.b}';
        $this->assertEquals('a', $c['c']);
        //variable referencing another variable
        $c['d'] = '${c}';
        $this->assertEquals('a', $c['d']);
    }

    public function testReading()
    {
        $data = [
            'a' => 'b',
            'c' => [
                'd' => 'e'
            ]
        ];
        //json
        $a = new Config();
        $a->readFile(__DIR__.'/configtest.json');
        $this->assertEquals($data, $a->get());
        //yaml
        $a = new Config();
        $a->readFile(__DIR__.'/configtest.yaml');
        $this->assertEquals($data, $a->get());
    }

    public function testSerializing()
    {
        $data = [
            'a' => 'b',
            'c' => [
                'd' => 'e'
            ]
        ];
        $c = new Config($data);
        //json
        $this->assertEquals($data, json_decode($c->json(), true));
        //yaml
        $this->assertEquals($data, Spyc::YAMLLoad($c->yaml()));
    }
}
