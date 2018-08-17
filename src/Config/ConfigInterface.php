<?php
/* FlatArray | https://gitlab.com/byjoby/flatarray | MIT License */
namespace FlatArray\Config;

interface ConfigInterface extends \ArrayAccess
{
    public function readFile($filename, string $name = null, bool $overwrite = false);
    public function json($raw = false) : string;
    public function yaml($raw = false) : string;
    public function get(string $name = null, bool $raw = false);
    public function merge($value, string $name = null, bool $overwrite = false);
}
