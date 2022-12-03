<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr\Config;

use Flatrr\FlatArrayInterface;

interface ConfigInterface extends FlatArrayInterface
{
    public function readDir(string $dir, string $name = null, bool $overwrite = false): void;
    public function readFile(string $filename, string $name = null, bool $overwrite = false): void;
    public function json(bool $raw = false): string;
    public function yaml(bool $raw = false): string;
    public function get(null|string $name = null, bool $raw = false): mixed;
    public function merge(mixed $value, string $name = null, bool $overwrite = false): static;
}
