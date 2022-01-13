<?php
/* Flatrr | https://gitlab.com/byjoby/flatrr | MIT License */

namespace Flatrr;

trait FlatArrayTrait
{
    private $_arrayData = array();
    private $_flattenCache = array();

    public function push(?string $name, $value)
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return;
        }
        if ($arr === null) {
            $arr = [];
        }
        $arr[] = $value;
        $this->set($name, $arr);
    }

    public function pop(?string $name)
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return;
        }
        $out = array_pop($arr);
        $this->set($name, $arr);
        return $out;
    }

    public function unshift(?string $name, $value)
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return;
        }
        if ($arr === null) {
            $arr = [];
        }
        array_unshift($arr, $value);
        $this->set($name, $arr);
    }

    public function shift(?string $name)
    {
        $arr = $this->flattenSearch($name);
        if ($arr !== null && !is_array($arr)) {
            return;
        }
        $out = array_shift($arr);
        $this->set($name, $arr);
        return $out;
    }

    public function set(?string $name, $value)
    {
        return $this->flattenSearch($name, $value);
    }

    public function get(?string $name = null)
    {
        return $this->flattenSearch($name);
    }

    function unset(?string $name)
    {
        $this->flattenSearch($name, null, true);
    }

    public function offsetSet($name, $value)
    {
        return $this->set($name, $value);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }

    public function offsetExists($name)
    {
        return $this->flattenSearch($name) !== null;
    }

    public function offsetUnset($name)
    {
        $this->unset($name);
    }

    public function rewind()
    {
        return reset($this->_arrayData);
    }

    public function current()
    {
        return current($this->_arrayData);
    }

    public function next()
    {
        return next($this->_arrayData);
    }

    public function key()
    {
        return key($this->_arrayData);
    }

    public function valid()
    {
        return isset($this->_arrayData[$this->key()]);
    }

    /**
     * Recursively set a value, with control over whether existing values or new
     * values take precedence
     */
    public function merge($value, string $name = null, bool $overwrite = false)
    {
        if (!isset($this[$name])) {
            //easiest possible outcome, old value doesn't exist, so we can just write the value
            $this->set($name, $value);
            return;
        } elseif (is_array($value) && is_array($this->flattenSearch($name))) {
            //both new and old values are arrays
            foreach ($value as $k => $v) {
                if ($name) {
                    $k = $name . '.' . $k;
                }
                $this->merge($v, $k, $overwrite);
            }
            return;
        } else {
            //old and new values exist, and one or both are not arrays, $overwrite rules the day
            if ($overwrite) {
                $this->set($name, $value);
            }
            return;
        }
    }

    /**
     * Recursively builds a reference down into $this->config from a config key
     * string. It sets it if $value exists, otherwise it returns the value if it
     * exists.
     */
    protected function flattenSearch(?string $name, $value = null, $unset = false)
    {
        if ($value !== null || $unset) {
            $this->_flattenCache = array();
        }
        if (!isset($this->_flattenCache[$name])) {
            $this->_flattenCache[$name] = $this->doFlattenSearch($name, $value, $unset);
        }
        return $this->_flattenCache[$name];
    }

    protected function doFlattenSearch(?string $name, $value = null, $unset = false)
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
                    $parent[$part] = array();
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
                //set the hard way
                if (!is_array($parent)) {
                    $parent = array();
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
