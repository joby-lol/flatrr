<?php

/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr;

class SelfReferencingFlatArray extends FlatArray
{
    /** @var array<string,string> */
    protected $cache = [];

    public function get(string $name = null, bool $raw = false, bool $unescape = true): mixed
    {
        $out = parent::get($name);
        if ($raw) {
            return $out;
        }
        $out = $this->filter($out);
        if ($unescape) {
            $out = $this->unescape($out);
        }
        return $out;
    }

    public function set(null|string $name, mixed $value): static
    {
        $this->cache = [];
        $this->filter(parent::set($name, $value));
        return $this;
    }

    public function pop(null|string $name): mixed
    {
        return $this->filter(parent::pop($name));
    }

    public function shift(null|string $name): mixed
    {
        return $this->filter(parent::shift($name));
    }

    public function current(): mixed
    {
        return $this->filter(parent::current());
    }

    protected function unescape(mixed $value): mixed
    {
        //map this function onto array values
        if (is_array($value)) {
            return array_map(
                [$this, 'unescape'],
                $value
            );
        }
        //search/replace on string values
        if (is_string($value)) {
            //unescape references
            $value = preg_replace(
                '/\$\\\?\{([^\}\\\]+)\\\?\}/S',
                '\${$1}',
                $value
            );
            //return
            return $value;
        }
        //fall back to just returning value, it's some other datatype
        return $value;
    }

    /**
     * Recursively replace ${var/name} type strings in string values with
     */
    protected function filter(mixed $value): mixed
    {
        //map this function onto array values
        if (is_array($value)) {
            return array_map(
                [$this, 'filter'],
                $value
            );
        }
        //search/replace on string values
        if (is_string($value) && strpos($value, '${') !== false) {
            //search for valid replacements
            return $this->cache[$value] ??
                ($this->cache[$value] = preg_replace_callback(
                    //search for things like ${var/name}, escape with \ before last brace
                    '/\$\{([^\}]*[^\.\\\])\}/',
                    //replace match with value from $this if it exists
                    [$this, 'filter_regex'],
                    //applied to $value
                    $value
                ));
        }
        //fall back to just returning value, it's some other datatype
        return $value;
    }

    /**
     * @param array<int,null|string> $matches
     * @return string
     */
    protected function filter_regex(array $matches): string
    {
        $value = $this->get($matches[1], false, false);
        if ($value !== null) {
            if (!is_array($value)) {
                return $value;
            }
        }
        return $matches[0];
    }
}
