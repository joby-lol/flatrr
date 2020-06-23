<?php
/* Flatrr | https://gitlab.com/byjoby/flatrr | MIT License */
namespace Flatrr;

class SelfReferencingFlatArray extends FlatArray
{
    public function get(string $name = null, bool $raw = false, $unescape = true)
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

    public function set(?string $name, $value)
    {
        return $this->filter(parent::set($name,$value));
    }

    public function push(?string $name, $value)
    {
        return $this->filter(parent::push($name,$value));
    }

    public function pop(?string $name)
    {
        return $this->filter(parent::pop($name));
    }

    public function unshift(?string $name, $value)
    {
        return $this->filter(parent::unshift($name,$value));
    }

    public function shift(?string $name)
    {
        return $this->filter(parent::shift($name));
    }

    public function rewind()
    {
        return $this->filter(parent::rewind());
    }

    public function next()
    {
        return $this->filter(parent::next());
    }

    public function current()
    {
        return $this->filter(parent::current());
    }

    protected function unescape($value)
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
    protected function filter($value)
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
            return preg_replace_callback(
                //search for things like ${var/name}, escape with \ before last brace
                '/\$\{([^\}]*[^\.\\\])\}/S',
                //replace match with value from $this if it exists
                [$this, 'filter_regex'],
                //applied to $value
                $value
            );
        }
        //fall back to just returning value, it's some other datatype
        return $value;
    }

    protected function filter_regex($matches)
    {
        if (null !== $value = $this->get($matches[1], false, false)) {
            if (!is_array($value)) {
                return $value;
            }
        }
        return $matches[0];
    }
}
