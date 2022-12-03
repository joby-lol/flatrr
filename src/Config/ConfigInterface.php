<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */
namespace Flatrr\Config;

interface ConfigInterface extends \ArrayAccess
{
    public function readFile($filename, string $name = null, bool $overwrite = false);
    public function json($raw = false) : string;
    public function yaml($raw = false) : string;
    public function get(string $name = null, bool $raw = false);
    public function merge($value, string $name = null, bool $overwrite = false);
}
