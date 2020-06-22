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

    protected function unescape($value)
    {
        if ($value === null) {
            return null;
        }
        //search/replace on string values
        if (is_string($value)) {
            //unescape references
            $value = preg_replace(
                '/\$\\\?\{([^\}\\\]+)\\\?\}/',
                '\${$1}',
                $value
            );
            //return
            return $value;
        }
        //map this function onto array values
        if (is_array($value)) {
            return array_map(
                function ($i) {
                    return $this->filter($i);
                },
                $value
            );
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
