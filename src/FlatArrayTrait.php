<?php

/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr;

trait FlatArrayTrait
{
    /** @var array<string|mixed> */
    protected $_arrayData = [];
    /** @var array<string|mixed> */
    protected $_flattenCache = [];

    public function push(null|string $name, mixed $value): static
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return $this;
        }
        if ($arr === null) {
            $arr = [];
        }
        $arr[] = $value;
        $this->set($name, $arr);
        return $this;
    }

    public function pop(null|string $name): mixed
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return null;
        }
        $out = array_pop($arr);
        $this->unset($name);
        $this->set($name, $arr);
        return $out;
    }

    public function unshift(null|string $name, mixed $value): static
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return $this;
        }
        if ($arr === null) {
            $arr = [];
        }
        array_unshift($arr, $value);
        $this->set($name, $arr);
        return $this;
    }

    public function shift(null|string $name): mixed
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return null;
        }
        $out = array_shift($arr);
        $this->unset($name);
        $this->set($name, $arr);
        return $out;
    }

    public function set(null|string $name, mixed $value): static
    {
        $this->flattenSearch($name, $value);
        return $this;
    }

    public function get(null|string $name = null): mixed
    {
        return $this->flattenSearch($name);
    }

    public function unset(null|string $name): static
    {
        $this->flattenSearch($name, null, true);
        return $this;
    }

    public function offsetSet($name, $value): void
    {
        $this->set($name, $value);
    }

    public function offsetGet($name): mixed
    {
        return $this->get($name);
    }

    public function offsetExists($name): bool
    {
        return $this->flattenSearch($name) !== null;
    }

    public function offsetUnset($name): void
    {
        $this->unset($name);
    }

    public function rewind(): void
    {
        reset($this->_arrayData);
    }

    public function current(): mixed
    {
        return current($this->_arrayData);
    }

    public function next(): void
    {
        next($this->_arrayData);
    }

    public function key(): null|string|int
    {
        return key($this->_arrayData);
    }

    public function valid(): bool
    {
        return isset($this->_arrayData[$this->key()]);
    }

    /**
     * Recursively set a value, with control over whether existing values or new
     * values take precedence
     */
    public function merge(mixed $value, string $name = null, bool $overwrite = false): static
    {
        if (!isset($this[$name])) {
            //easiest possible outcome, old value doesn't exist, so we can just write the value
            $this->set($name, $value);
        } elseif (is_array($value) && is_array($this->flattenSearch($name))) {
            //both new and old values are arrays
            foreach ($value as $k => $v) {
                if ($name) {
                    $k = $name . '.' . $k;
                }
                $this->merge($v, $k, $overwrite);
            }
        } else {
            //old and new values exist, and one or both are not arrays, $overwrite rules the day
            if ($overwrite) {
                $this->set($name, $value);
            }
        }
        return $this;
    }

    /**
     * Recursively builds a reference down into $this->config from a config key
     * string. It sets it if $value exists, otherwise it returns the value if it
     * exists.
     */
    protected function flattenSearch(null|string $name, mixed $value = null, bool $unset = false): mixed
    {
        if ($value !== null || $unset) {
            $this->_flattenCache = [];
        }
        if (!isset($this->_flattenCache[$name])) {
            $this->_flattenCache[$name] = $this->doFlattenSearch($name, $value, $unset);
        }
        return $this->_flattenCache[$name];
    }

    protected function doFlattenSearch(null|string $name, mixed $value = null, bool $unset = false): mixed
    {
        //check for home strings
        if ($name == '' || $name === null) {
            if ($unset) {
                $this->_arrayData = [];
            } elseif ($value !== null) {
                $this->_arrayData = $value;
            }
            return $this->_arrayData;
        }
        //build a reference to where this name should be
        $parent = &$this->_arrayData;
        $name = explode('.', $name);
        $key = array_pop($name);
        if ($value !== null) {
            foreach ($name as $part) {
                if (!isset($parent[$part])) {
                    $parent[$part] = [];
                }
                $parent = &$parent[$part];
            }
        } else {
            foreach ($name as $part) {
                if (!isset($parent[$part])) {
                    return null;
                }
                $parent = &$parent[$part];
            }
        }
        //now we have a ref, see if we can unset or set it
        if ($unset) {
            unset($parent[$key]);
            return null;
        } elseif ($value !== null) {
            if (is_array(@$parent[$key]) && is_array($value)) {
                //both value and destination are arrays, merge them
                $parent[$key] = array_replace_recursive($parent[$key], $value);
            } else {
                //destination is not an array, to set this we must overwrite it with an empty array
                if (!is_array(@$parent[$key])) {
                    $parent[$key] = [];
                }
                $parent[$key] = $value;
            }
            return null;
        }
        //return value
        if (!is_array($parent)) {
            return null;
        }
        return @$parent[$key];
    }
}
