<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

declare(strict_types=1);

namespace Flatrr\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

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
        $a->readFile(__DIR__ . '/configtest.json');
        $this->assertEquals($data, $a->get());
        //yaml
        $a = new Config();
        $a->readFile(__DIR__ . '/configtest.yaml');
        $this->assertEquals($data, $a->get());
        //nonexistant files
        $a = new Config();
        $a->readFile(__DIR__ . '/does-not-exist.json');
        $a->readFile(__DIR__ . '/does-not-exist.yaml');
        $this->assertEquals([], $a->get());
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
        $this->assertEquals($data, Yaml::parse($c->yaml()));
    }

    public function testReadingDirectory()
    {
        $config = new Config;
        $config->readDir(__DIR__ . '/nonexistantdir');
        $this->assertEquals([], $config->get());
        $config->readDir(__DIR__ . '/configtestdir');
        $this->assertEquals('b', $config['ini_file.a']);
        $this->assertEquals('a', $config['yaml_file']);
        $this->assertEquals('a', $config['json_file']);
        $this->assertEquals('a', $config['yml_file']);
    }
}
