<?php declare(strict_types=1);

/*
 * This file is part of the pinepain/php-object-maps PHP library.
 *
 * Copyright (c) 2016-2017 Bogdan Padalko <pinepain@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit http://opensource.org/licenses/MIT
 */


namespace Pinepain\ObjectMaps\Tests;


use Pinepain\ObjectMaps\ObjectBiMap;
use Pinepain\ObjectMaps\ObjectBiMapInterface;
use Pinepain\ObjectMaps\ObjectMap;
use Pinepain\ObjectMaps\ObjectMapInterface;
use stdClass;


class ObjectBiMapTest extends AbstractObjectMapInterfaceTest
{
    public function buildMap(int $behavior = ObjectMapInterface::DEFAULT)
    {
        return new ObjectBiMap($behavior);
    }

    public function testValuesForRegularMap()
    {
        $map = $this->buildMap();

        $key   = new stdClass();
        $value = new stdClass();

        $map->put($key, $value);

        $this->assertTrue($map->has($key));
        $this->assertFalse($map->has($value));

        $vmap = $map->values();

        $this->assertInstanceOf(ObjectBiMapInterface::class, $vmap);

        $this->assertFalse($vmap->has($key));
        $this->assertTrue($vmap->has($value));

        $map->clear();

        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());

        $vmap->put($key, $value);

        $this->assertSame(1, $vmap->count());
        $this->assertSame(1, $map->count());

        $this->assertFalse($map->has($key));
        $this->assertTrue($map->has($value));

        $key = null;
        $this->assertSame(1, $vmap->count());
        $this->assertSame(1, $map->count());

        $value = null;
        $this->assertSame(1, $vmap->count());
        $this->assertSame(1, $map->count());
    }

    public function testValuesForWeakKeyMap()
    {
        $map = $this->buildMap(ObjectBiMapInterface::WEAK_KEY);

        $key   = new stdClass();
        $value = new stdClass();

        $map->put($key, $value);

        $this->assertSame(1, $map->count());

        $vmap = $map->values();

        $this->assertInstanceOf(ObjectBiMapInterface::class, $vmap);

        $this->assertFalse($vmap->has($key));
        $this->assertTrue($vmap->has($value));

        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $value = null;
        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $key = null;
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());

        $key   = new stdClass();
        $value = new stdClass();

        $vmap->put($key, $value);

        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $key = null;
        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $value = null;
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());
    }

    public function testValuesForWeakValueMap()
    {
        $map = $this->buildMap(ObjectBiMapInterface::WEAK_VALUE);

        $key   = new stdClass();
        $value = new stdClass();

        $map->put($key, $value);

        $this->assertSame(1, $map->count());

        $vmap = $map->values();

        $this->assertInstanceOf(ObjectBiMapInterface::class, $vmap);

        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $key = null;
        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $value = null;
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());

        $key   = new stdClass();
        $value = new stdClass();

        $vmap->put($key, $value);

        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $value = null;
        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $key = null;
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());
    }


    public function testValuesForWeakKeyValueMap()
    {
        $map = $this->buildMap(ObjectBiMapInterface::WEAK_KEY_VALUE);

        $key_1   = new stdClass();
        $value_1 = new stdClass();

        $key_2   = new stdClass();
        $value_2 = new stdClass();

        $map->put($key_1, $value_1);
        $map->put($key_2, $value_2);

        $this->assertSame(2, $map->count());

        $vmap = $map->values();

        $this->assertInstanceOf(ObjectBiMapInterface::class, $vmap);

        $this->assertSame(2, $map->count());
        $this->assertSame(2, $vmap->count());

        $key_1 = null;
        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $value_2 = null;
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());

        $key_1   = new stdClass();
        $value_1 = new stdClass();

        $key_2   = new stdClass();
        $value_2 = new stdClass();

        $vmap->put($key_1, $value_1);
        $vmap->put($key_2, $value_2);

        $this->assertSame(2, $map->count());
        $this->assertSame(2, $vmap->count());

        $key_1 = null;
        $this->assertSame(1, $map->count());
        $this->assertSame(1, $vmap->count());

        $value_2 = null;
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $vmap->count());
    }

    /**
     * @expectedException \Pinepain\ObjectMaps\Exceptions\OverflowException
     * @expectedExceptionMessage Key with such value already exists
     */
    public function testKeyValuesOutOfSyncPutFails()
    {
        $key_1   = new stdClass();
        $value_1 = new stdClass();

        $key_2   = new stdClass();
        $value_2 = new stdClass();

        $keys_map   = new ObjectMap();
        $values_map = new ObjectMap();

        $keys_map->put($key_1, $value_1);
        $values_map->put($value_2, $key_2);

        $map = new class($keys_map, $values_map) extends ObjectBiMap
        {
            public function __construct(ObjectMapInterface $keys, ObjectMapInterface $values)
            {
                $this->keys   = $keys;
                $this->values = $values;
            }
        };

        $map->put($key_2, $value_2);
    }

    /**
     * @expectedException \Pinepain\ObjectMaps\Exceptions\OutOfBoundsException
     * @expectedExceptionMessage Key with such value not found
     */
    public function testKeyValuesOutOfSyncRemoveFails()
    {
        $key_1   = new stdClass();
        $value_1 = new stdClass();

        $key_2   = new stdClass();
        $value_2 = new stdClass();

        $keys_map   = new ObjectMap();
        $values_map = new ObjectMap();

        $keys_map->put($key_1, $value_1);
        $values_map->put($value_2, $key_2);

        $map = new class($keys_map, $values_map) extends ObjectBiMap
        {
            public function __construct(ObjectMapInterface $keys, ObjectMapInterface $values)
            {
                $this->keys   = $keys;
                $this->values = $values;
            }
        };

        $map->remove($key_1);
    }
}
