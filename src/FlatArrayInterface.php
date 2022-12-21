<?php

/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr;

use ArrayAccess;
use Iterator;

/**
 * @extends ArrayAccess<string,mixed>
 * @extends Iterator<string,mixed>
 */
interface FlatArrayInterface extends ArrayAccess, Iterator
{
    public function set(null|string $name, mixed $value): mixed;
    public function get(null|string $name = null): mixed;
    public function unset(null|string $name): static;
    public function merge(mixed $value, string $name = null, bool $overwrite = false): static;

    public function push(null|string $name, mixed $value): static;
    public function pop(null|string $name): mixed;
    public function unshift(null|string $name, mixed $value): static;
    public function shift(null|string $name): mixed;
}
