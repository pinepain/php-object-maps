# Weak-referenced data structures for PHP [![Build Status](https://travis-ci.org/pinepain/php-weak-lib.svg)](https://travis-ci.org/pinepain/php-weak-lib)

This library is based on [php-weak][php-weak-ext] PHP extension and provides various weak data structures:

 - [class `Weak\WeakKeyMap`](#class-weakweakkeymap)
 - [class `Weak\WeakValueMap`](#class-weakweakvaluemap)
 - [class `Weak\WeakKeyValueMap`](#class-weakweakkeyvaluemap)


## Requirements

[php-weak][php-weak-ext] PHP extension required. PHP 7 only (due to php-weak).


## Installation:

`composer require pinepain/php-weak-lib`


## Docs:

#### Class `Weak\WeakKeyMap`

Mapping class that references keys weakly. Entries will be discarded when there is no longer a reference to the key.
This can be used to associate additional data with an object owned by other parts of an application without adding
attributes to those objects. This can be especially useful with objects that override attribute accesses.
Built on top of [`SplObjectStorage`][php-SplObjectStorage].

##### Caution

Because a `Weak\WeakKeyMap` is built on top of a `SplObjectStorage`, it must not change size when
iterating over it. This can be difficult to ensure for a `Weakref\WeakKeyMap` because actions performed by the program
during iteration may cause items in the storage to vanish "by magic" (as a side effect of garbage collection).

##### Example

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Weak\WeakKeyMap;

$map = new WeakKeyMap();

$obj1 = new stdClass();
$inf1 = new stdClass();

$obj2 = new stdClass();
$inf2 = new stdClass();

$map->attach($obj1, $inf1);
$map->attach($obj2, $inf2);

var_dump($map->count()); // 2

// Let's destroy key
$obj1 = null;

var_dump($map->count()); // 1
```


#### Class `Weak\WeakValueMap`

Mapping class that references values weakly. Entries will be discarded when reference to the value exists any more.
Built on top of [`SplObjectStorage`][php-SplObjectStorage].

##### Caution

Because a `Weak\WeakValueMap` is built on top of a `SplObjectStorage`, it must not change size when
iterating over it. This can be difficult to ensure for a `Weakref\WeakValueMap` because actions performed by the program
during iteration may cause items in the storage to vanish "by magic" (as a side effect of garbage collection).

##### Example

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Weak\WeakValueMap;

$map = new WeakValueMap();

$obj1 = new stdClass();
$inf1 = new stdClass();

$obj2 = new stdClass();
$inf2 = new stdClass();

$map->attach($obj1, $inf1);
$map->attach($obj2, $inf2);

var_dump($map->count()); // 2

// Let's destroy value
$inf1 = null;

var_dump($map->count()); // 1
```


#### Class `Weak\WeakKeyValueMap`

Mapping class that references values weakly. Entries will be discarded when reference to the key or value exists any more.
Built on top of [`SplObjectStorage`][php-SplObjectStorage].

##### Caution

Because a `Weak\WeakKeyValueMap` is built on top of a `SplObjectStorage`, it must not change size when
iterating over it. This can be difficult to ensure for a `Weakref\WeakKeyValueMap` because actions performed by the program
during iteration may cause items in the storage to vanish "by magic" (as a side effect of garbage collection).

##### Example

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Weak\WeakValueMap;

$map = new WeakKeyValueMap();

$obj1 = new stdClass();
$inf1 = new stdClass();

$obj2 = new stdClass();
$inf2 = new stdClass();

$map->attach($obj1, $inf1);
$map->attach($obj2, $inf2);

var_dump($map->count()); // 2

// Let's destroy key
$obj1 = null;

var_dump($map->count()); // 1

// Let's destroy value
$inf2 = null;

var_dump($map->count()); // 0
```

## License

[php-weak-lib](https://github.com/pinepain/php-weak-lib) PHP library is a free software licensed under the [MIT license](http://opensource.org/licenses/MIT).

[php-weak-ext]: https://github.com/pinepain/php-weak
[php-SplObjectStorage]: http://php.net/manual/en/class.splobjectstorage.php
[js-WeakMap]: https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/WeakMap
