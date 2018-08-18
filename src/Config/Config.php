<?php
/* Flatrr | https://gitlab.com/byjoby/flatrr | MIT License */
namespace Flatrr\Config;

use Symfony\Component\Yaml\Yaml;
use Flatrr\SelfReferencingFlatArray;
use Flatrr\FlatArray;

class Config extends SelfReferencingFlatArray implements ConfigInterface
{
    protected function parse(string $input, string $format) : array
    {
        $fn = 'parse_'.$format;
        if (!method_exists($this, $fn)) {
            throw new \Exception("Don't know how to parse the format \"$format\"");
        }
        if ($out = $this->$fn($input)) {
            return $out;
        }
        return array();
    }

    protected function parse_yaml($input)
    {
        return Yaml::parse($input);
    }

    public function json($raw = false) : string
    {
        return json_encode($this->get(null, $raw), JSON_PRETTY_PRINT);
    }

    public function yaml($raw = false) : string
    {
        return Yaml::dump($this->get(null, $raw), 10);
    }

    protected function read_ini($filename)
    {
        return parse_ini_file($filename, true);
    }

    protected function read_json($filename)
    {
        return json_decode(file_get_contents($filename), true);
    }

    protected function read_yaml($filename)
    {
        return Yaml::parse(file_get_contents($filename));
    }

    protected function read_yml($filename)
    {
        return $this->read_yaml($filename);
    }

    public function readFile($filename, string $name = null, bool $overwrite = false)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \Exception("Couldn't read config file \"$filename\"");
        }
        $format = strtolower(preg_replace('/.+\./', '', $filename));
        $fn = 'read_'.$format;
        if (!method_exists($this, $fn)) {
            throw new \Exception("Don't know how to read the format \"$format\"");
        }
        $data = $this->$fn($filename);
        if (!$data) {
            throw new \Exception("Error reading \"".$filename."\"");
        }
        $this->merge($data, $name, $overwrite);
    }
}
