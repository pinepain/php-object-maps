# Weak-referenced data structures for PHP based on Ref php extension

[![Build Status](https://travis-ci.org/pinepain/php-object-maps.svg?branch=master)](https://travis-ci.org/pinepain/php-object-maps)
[![Code Coverage](https://scrutinizer-ci.com/g/pinepain/php-object-maps/badges/coverage.png?b=refactor)](https://scrutinizer-ci.com/g/pinepain/php-object-maps/?branch=refactor)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/72be40cb-1d0f-48db-b89c-c99ea007bf63/mini.png)](https://insight.sensiolabs.com/projects/72be40cb-1d0f-48db-b89c-c99ea007bf63)

### PLEASE READ:

Maintaining this project takes significant amount of time and efforts.
If you like my work and want to show your appreciation, please consider supporting me at https://www.patreon.com/pinepain.

## Requirements

 - PHP >= 7.2
 - (optional, required only for maps weak behavior) [php-ref][php-ref-ext] extension


## Installation:

    composer require pinepain/php-object-maps

## Docs:

This library offers two main classes: `ObjectMap` and `ObjectBiMap`.
They are what their name is - classic object maps which map object keys to object values.

The key difference between
`ObjectMap` and `ObjectBiMap` is that `ObjectBiMap` require all values to be unique and it offers you to get mirrored 
`ObjectBiMap` map with keys and values from source map flipped. Note, that flipped map will still maintain connection to
the original one and thus any modification to any `ObjectBiMap` in a chain will be reflected on all chain.  

### Maps behavior

Both `ObjectMap` and `ObjectBiMap` offers weak variations ([php-ref][php-ref-ext] extension required) which could be specified
by passing one of `ObjectMapInterface::{WEAK_KEY,WEAK_VALUE,WEAK_KEY_VALUE}` constants to the constructor.
By default no weakness enabled. Note, that when weak behavior enable on key or/and value, their refcount won't be
incremented by map internals and thus it is possible that they will be destructed without a need be purged from map.

`WEAK_KEY` means that key-value pair will be removed as long as key will be destructed. `WEAK_VALUE` is the same for value
and `WEAK_KEY_VALUE` will trigger removal when key or value will be destructed.

For more details see [tests](./tests).

## License

[php-object-maps](https://github.com/pinepain/php-object-maps) PHP library is licensed under the [MIT license](http://opensource.org/licenses/MIT).

[php-ref-ext]: https://github.com/pinepain/php-ref
