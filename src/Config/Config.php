<?php

/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr\Config;

use Flatrr\SelfReferencingFlatArray;
use Spyc;

class Config extends SelfReferencingFlatArray implements ConfigInterface
{
    public function readDir(string $dir, string $name = null, bool $overwrite = false): static
    {
        $dir = realpath($dir);
        if ($dir && is_dir($dir)) {
            $glob = glob("$dir/*");
            if ($glob) {
                foreach ($glob as $f) {
                    if (is_file($f)) {
                        $this->readFile($f, $name, $overwrite);
                    }
                }
            }
        }
        return $this;
    }

    public function json(bool $raw = false): string
    {
        return json_encode($this->get(null, $raw), JSON_PRETTY_PRINT); // @phpstan-ignore-line
    }

    public function yaml(bool $raw = false): string
    {
        return Spyc::YAMLDump($this->get(null, $raw), 2);
    }

    /** @return array<mixed|mixed> */
    protected function read_ini(string $filename): false|array
    {
        return parse_ini_file($filename, true);
    }

    /** @return array<mixed|mixed> */
    protected function read_json(string $filename): null|array
    {
        /** @var string */
        $data = file_get_contents($filename);
        return json_decode($data, true);
    }

    /** @return array<mixed|mixed> */
    protected function read_yaml(string $filename): array
    {
        return Spyc::YAMLLoad($filename);
    }

    /** @return array<mixed|mixed> */
    protected function read_yml(string $filename): array
    {
        return $this->read_yaml($filename);
    }

    public function readFile(string $filename, string $name = null, bool $overwrite = false): static
    {
        $format = strtolower(preg_replace('/.+\./', '', $filename));
        $fn = 'read_' . $format;
        if (is_file($filename) && is_readable($filename) && method_exists($this, $fn)) {
            $data = $this->$fn($filename);
            if ($data !== null) {
                $this->merge($data, $name, $overwrite);
            }
        }
        return $this;
    }
}
