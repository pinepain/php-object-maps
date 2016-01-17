<?php

/*
 * This file is part of the pinepain/php-weak-lib PHP library.
 *
 * Copyright (c) 2016 Bogdan Padalko <zaq178miami@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit http://opensource.org/licenses/MIT
 */

namespace Weak\Tests;

use PHPUnit_Framework_TestCase;
use SplObjectStorage;
use stdClass;
use Weak\HashedReference;
use Weak\Reference;
use Weak\WeakKeyMap;

class WeakKeyMapTest extends PHPUnit_Framework_TestCase
{
    public function testGetHash()
    {
        $map = new WeakKeyMap();

        $obj = new stdClass();

        // we need this to update referent object handlers HashTable so it changes it value and gives us new hash, now consistent
        new Reference($obj);

        $expected_hash = spl_object_hash($obj);

        $this->assertSame($expected_hash, $map->getHash($obj));

        $wr = new HashedReference($obj);

        $this->assertSame($expected_hash, $map->getHash($wr));

        $obj = null;

        $this->assertNull($wr->get());
        $this->assertSame($expected_hash, $map->getHash($wr));
    }

    public function testAttach()
    {
        $map = new WeakKeyMap();

        $obj1 = new stdClass();
        $map->attach($obj1);

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertNull($map[$obj1]);

        $obj2 = new stdClass();
        $map->attach($obj2, 'data');

        $this->assertCount(2, $map);
        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($map[$obj2], 'data');
    }

    public function testOffsetSet()
    {
        $map = new WeakKeyMap();

        $obj1 = new stdClass();
        $map[$obj1] = null;

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertNull($map[$obj1]);

        $obj2 = new stdClass();
        $map[$obj2] = 'data';

        $this->assertCount(2, $map);
        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($map[$obj2], 'data');

        $obj2 = null;
        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj1));

        $obj1 = null;
        $this->assertCount(0, $map);
    }

    public function testOffsetUnset()
    {
        $map = new WeakKeyMap();

        $obj1 = new stdClass();
        $map[$obj1] = null;
        $obj2 = new stdClass();
        $map[$obj2] = 'data';

        $this->assertCount(2, $map);

        unset($map[$obj1]);
        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj2));

        unset($map[$obj2]);
        $this->assertCount(0, $map);
    }

    public function testObjectRemoved()
    {
        $map = new WeakKeyMap();

        $obj1 = new stdClass();
        $inf1 = '$obj1';

        $obj2 = new stdClass();
        $inf2 = '$obj2';

        $map->attach($obj1, $inf1);
        $map->attach($obj2, $inf2);

        $this->assertCount(2, $map);
        $map->rewind();
        $this->assertSame($obj1, $map->current());
        $this->assertSame($inf1, $map->getInfo());
        $map->next();
        $this->assertSame($obj2, $map->current());
        $this->assertSame($inf2, $map->getInfo());

        $obj1 = null;

        $this->assertCount(1, $map);
        $map->rewind();
        $this->assertSame($obj2, $map->current());
        $this->assertSame($inf2, $map->getInfo());
    }

    public function testGetCurrent()
    {
        $map = new WeakKeyMap();

        $obj1 = new class
        {
            function __destruct()
            {
                throw new \Exception('Prevent weakref handler to be executed');
            }
        };

        $inf1 = '$obj1';

        $obj2 = new stdClass();
        $inf2 = '$obj2';

        $map->attach($obj1, $inf1);
        $map->attach($obj2, $inf2);

        $this->assertCount(2, $map);
        $map->rewind();
        $this->assertSame($obj1, $map->current());
        $this->assertSame($inf1, $map->getInfo());
        $map->next();
        $this->assertSame($obj2, $map->current());
        $this->assertSame($inf2, $map->getInfo());

        try {
            $obj1 = null;
        } catch (\Exception $e) {
            $this->assertSame('Prevent weakref handler to be executed', $e->getMessage());
        }

        $this->assertCount(2, $map);
        $map->rewind();
        $this->assertNull($map->current());
        $this->assertSame($inf1, $map->getInfo());
        $map->next();
        $this->assertSame($obj2, $map->current());
        $this->assertSame($inf2, $map->getInfo());
    }

    public function testAddAll()
    {
        $map = new WeakKeyMap();
        $storage = new SplObjectStorage();

        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $storage->attach($obj1);
        $storage->attach($obj2);

        $this->assertCount(2, $storage);

        $map->addAll($storage);

        $this->assertCount(2, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));

        $storage = null;

        $obj1 = null;

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj2));


        $obj2 = null;
        $this->assertCount(0, $map);
    }

    public function testRemoveAll()
    {
        $map = new WeakKeyMap();
        $storage = new SplObjectStorage();

        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $storage->attach($obj1);

        $this->assertCount(1, $storage);

        $map->attach($obj1);
        $map->attach($obj2);
        $this->assertCount(2, $map);

        $map->removeAll($storage);

        $this->assertCount(1, $map);
        $this->assertFalse($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));
    }

    public function testRemoveAllExcept()
    {
        $map = new WeakKeyMap();
        $storage = new SplObjectStorage();

        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $storage->attach($obj1);

        $this->assertCount(1, $storage);

        $map->attach($obj1);
        $map->attach($obj2);
        $this->assertCount(2, $map);

        $map->removeAllExcept($storage);

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertFalse($map->contains($obj2));
    }
}
