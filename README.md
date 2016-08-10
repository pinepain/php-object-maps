# Weak-referenced data structures for PHP based on Ref php extension

[![Build Status](https://travis-ci.org/pinepain/php-ref-lib.svg)](https://travis-ci.org/pinepain/php-ref-lib)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pinepain/php-ref-lib/badges/quality-score.png)](https://scrutinizer-ci.com/g/pinepain/php-ref-lib)
[![Code Coverage](https://scrutinizer-ci.com/g/pinepain/php-ref-lib/badges/coverage.png)](https://scrutinizer-ci.com/g/pinepain/php-ref-lib)

This library is based on [php-ref][php-ref-ext] PHP extension and provides various weak data structures:

 - [class `Ref\WeakKeyMap`](#class-refweakkeymap)
 - [class `Ref\WeakValueMap`](#class-refweakvaluemap)
 - [class `Ref\WeakKeyValueMap`](#class-refweakkeyvaluemap)


## Requirements

PHP >= 7.0 and [php-ref][php-ref-ext] extension installed required.


## Installation:

    composer require pinepain/php-ref-lib


## Docs:


#### Class `Ref\WeakKeyMap`

Mapping class that references keys weakly. Entries will be discarded when there is no longer any references to the keyleft.
This can be used to associate additional data with an object owned by other parts of an application without adding
attributes to those objects. This can be especially useful with objects that override attribute accesses.
Built on top of [`SplObjectStorage`][php-SplObjectStorage].

##### Example

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Ref\WeakKeyMap;

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

#### Class `Ref\WeakValueMap`

Mapping class that references values weakly. Entries will be discarded when no more reference to the value exist any more.
Built on top of [`SplObjectStorage`][php-SplObjectStorage].

##### Example

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Ref\WeakValueMap;

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


#### Class `Ref\WeakKeyValueMap`

Mapping class that references values weakly. Entries will be discarded when reference to the key or value exists any more.
Built on top of [`SplObjectStorage`][php-SplObjectStorage].

##### Example

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Ref\WeakKeyValueMap;

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

#### Caution

Because `Ref\WeakKeyMap`,  `Ref\WeakValueMap` and `Ref\WeakKeyValueMap` classes are built on top of a `SplObjectStorage`,
they must not change size during iterating over it. This can be difficult to ensure because actions performed during the
iteration may cause items in the storage to vanish in a non-obvious way as a side effect of garbage collection.


## License

[php-ref-lib](https://github.com/pinepain/php-ref-lib) PHP library is licensed under the [MIT license](http://opensource.org/licenses/MIT).

[php-ref-ext]: https://github.com/pinepain/php-ref
[php-SplObjectStorage]: http://php.net/manual/en/class.splobjectstorage.php
[js-WeakMap]: https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/WeakMap
