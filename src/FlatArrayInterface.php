<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr;

use ArrayAccess;
use Iterator;

/** 
 * @extends ArrayAccess<string|mixed>
 * @extends Iterator<string|mixed>
 */
interface FlatArrayInterface extends ArrayAccess, Iterator
{
    public function set(?string $name, $value);
    public function get(?string $name = null);
    public function unset(?string $name);
    public function merge($value, string $name = null, bool $overwrite = false);

    public function push(?string $name, $value);
    public function pop(?string $name);
    public function unshift(?string $name, $value);
    public function shift(?string $name);
}
