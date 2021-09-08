<?php
/* Flatrr | https://gitlab.com/byjoby/flatrr | MIT License */

namespace Flatrr\Config;

use Flatrr\SelfReferencingFlatArray;
use Spyc;

class Config extends SelfReferencingFlatArray implements ConfigInterface
{
    public $strict = false;

    public function readDir($dir, string $name = null, bool $overwrite = false)
    {
        $dir = realpath($dir);
        if (!$dir || !is_dir($dir)) {
            return;
        }
        foreach (glob("$dir/*") as $f) {
            if (is_file($f)) {
                $this->readFile($f, $name, $overwrite);
            }
        }
    }

    protected function parse(string $input, string $format): array
    {
        $fn = 'parse_' . $format;
        if (!method_exists($this, $fn)) {
            if ($this->strict) {
                throw new \Exception("Don't know how to parse the format \"$format\"");
            } else {
                return null;
            }
        }
        if ($out = $this->$fn($input)) {
            return $out;
        }
        return array();
    }

    protected function parse_yaml($input)
    {
        return Spyc::YAMLLoadString($input);
    }

    public function json($raw = false): string
    {
        return json_encode($this->get(null, $raw), JSON_PRETTY_PRINT);
    }

    public function yaml($raw = false): string
    {
        return Spyc::YAMLDump($this->get(null, $raw), 2);
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
        return Spyc::YAMLLoad($filename);
    }

    protected function read_yml($filename)
    {
        return $this->read_yaml($filename);
    }

    public function readFile($filename, string $name = null, bool $overwrite = false)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            if ($this->strict) {
                throw new \Exception("Couldn't read config file \"$filename\"");
            } else {
                return null;
            }
        }
        $format = strtolower(preg_replace('/.+\./', '', $filename));
        $fn = 'read_' . $format;
        if (!method_exists($this, $fn)) {
            if ($this->strict) {
                throw new \Exception("Don't know how to read the format \"$format\"");
            } else {
                return null;
            }
        }
        $data = $this->$fn($filename);
        if (!$data) {
            if ($this->strict) {
                throw new \Exception("Error reading \"" . $filename . "\"");
            } else {
                return null;
            }
        }
        $this->merge($data, $name, $overwrite);
    }
}
