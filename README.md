# Flatrr

[![Build Status](https://travis-ci.org/jobyone/flatrr.svg?branch=main)](https://travis-ci.org/jobyone/flatrr)

## What Flatrr does

Flatrr is a utility library for accessing arrays via flattened key names. So, for example, rather than using `$arr['foo']['bar']` you could use `$arr['foo.bar']`. Mostly this is useful if you want to use string building to make the keys you're going to use to access an array.

It should be noted that because of the way arrays and references work, this is not going to work *exactly* the same way as a native array in all cases. There are actually countless tiny caveats, and with that in mind you should generally stick to using this library as it is documented. Using undocumented features is exceptionally unpredictable due to the nature of this tool, and things may work radically different under the hood in the future.

## FlatArray

The main FlatArray class is very simple. It only implements \Array and \Iterator. With it you can make arrays, merge values into them, and retrieve parts of the array via flattened keys.

### Constructing and getting values

```php
// Instantiating FlatArrays can be done by passing them an array
// Keys in the initial array will be unflattened, for example the following
// yields a FlatArray containing ['foo'=>'bar','bar'=>['baz'=>'a','buz'=>'u']]
$f = new \Flatrr\FlatArray([
  'foo' => 'bar',
  'bar.baz' => 'a',
  'bar.buz' => 'u'
]);
```

You can then access the values of the array like a normal array, through the `get()` method, or by flattened keys.

```php
// All of the following are equal to 'a'
$f['bar']['baz'];
$f['bar.baz'];
$f->get('bar.baz');
```

### Setting values

Values can be set through either flattened keys or the `set()` method. Setting values like a normal multi-dimensional array won't work beyond the first layer, and it shouldn't be done.

```php
// Both of these work
$f['foo.bar'] = 'baz';
$f->set('foo.bar','baz');
// This does NOT work
$f['foo']['bar'] = 'baz';
```

## SelfReferencingFlatArray

SelfReferencingFlatArray is a class that does everything FlatArray does, but also allows strings within the array to reference other fields within the array, and include them as strings. For example:

```php
$f = new \Flatrr\SelfReferencingFlatArray([
  'foo.bar' => 'baz',
  'foo.baz' => '${foo.bar}'
]);
// foo.baz will now always return the value of foo.bar
// echoes 'baz'
echo $f['foo.baz'];
// You can also get the "raw" value of a field using get()
// echoes '${foo.bar}'
echo $f->get('foo.baz');
// Variables can also be used as part of other strings
$f['a'] = 'foo.bar is: ${foo.bar}';
// echoes 'foo.bar is: baz'
echo $f['a'];
// Variables are resolved recursively
$f['b'] = 'foo.baz is: ${foo.baz}';
// echoes 'foo.baz is: baz'
echo $f['b'];
```

## Config

Config does the same things as SelfReferencingFlatArray, but adds methods for reading INI, JSON, and YAML files. It also provides methods for extracting its contents as JSON and YAML.
