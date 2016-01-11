# Weak-referenced data structures for PHP [![Build Status](https://travis-ci.org/pinepain/php-weak-lib.svg)](https://travis-ci.org/pinepain/php-weak-lib)

This library base on [php-weak][php-weak-ext] PHP extension and provides various weak data types.

Currently only weak version of [`SplObjectStorage`][php-SplObjectStorage] provided, which is basically a
[`WeakMap`][js-WeakMap] in terms of ECMA 2015, but for PHP.

## About:

`Weak\SplObjectStorage` aims to be a simple drop-in replacement for SPL `SplObjectStorage` when you need to remove
key (and it optional value) after key object GCed. This is useful when you want to organize some runtime caching or
doing components integrations which converts one object into another and have to keep transformation table of one into
another. But basically it is `WeakMap` or `WeakHashMap`, depends of what languages you are familiar with. If you don't
set any value for object than it will be just a `WeakSet` for you.

In short: when key object GCed, it will be removed from `Weak\SplObjectStorage`

## Installation:

`composer require pinepain/php-weak-lib`

## Example:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Weak\SplObjectStorage;

$storage = new SplObjectStorage();

$obj = new stdClass();
$obj->obj = 'I am object';

$inf = new stdClass();
$inf->inf = 'I am info';

$storage->attach($obj, $inf);

var_dump($storage->count()); // 1

// Let's destroy object
$obj = null;

var_dump($storage->count()); // 0
```
    
## License

[php-weak-lib](https://github.com/pinepain/php-weak-lib) PHP library is a free software licensed under the [MIT license](http://opensource.org/licenses/MIT).

[php-weak-ext]: https://github.com/pinepain/php-weak
[php-SplObjectStorage]: http://php.net/manual/en/class.splobjectstorage.php
[js-WeakMap]: https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/WeakMap
