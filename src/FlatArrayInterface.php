<?php
/* FlatArray | https://gitlab.com/byjoby/flatarray | MIT License */
namespace FlatArray;

interface FlatArrayInterface extends \ArrayAccess, \Iterator
{
    public function __construct(array $data = null);
    public function set(?string $name, $value);
    public function get(?string $name = null);
    public function unset(?string $name);
    public function merge($value, string $name = null, bool $overwrite = false);

    public function push(?string $name, $value);
    public function pop(?string $name);
    public function shift(?string $name, $value);
    public function unshift(?string $name);
}
