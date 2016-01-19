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
use Weak\WeakKeyValueMap;

class WeakKeyValueMapTest extends PHPUnit_Framework_TestCase
{
    public function testAttach()
    {
        $map = new WeakKeyValueMap();

        $obj = new stdClass();
        $inf = new stdClass();
        $map->attach($obj, $inf);

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj));
        $this->assertTrue(isset($map[$obj]));
        $this->assertSame($inf, $map[$obj]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage WeakKeyValueMap expects data to be object, NULL given
     */
    public function testAttachNoData()
    {
        $map = new WeakKeyValueMap();

        $obj = new stdClass();
        $map->attach($obj);
    }

    public function testInfoRemoved()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map->attach($obj1, $inf1);

        $obj2 = new stdClass();
        $inf2 = new stdClass();
        $map->attach($obj2, $inf2);

        $this->assertCount(2, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1, $map[$obj1]);

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);

        $inf1 = null;

        $this->assertCount(1, $map);

        $this->assertFalse($map->contains($obj1));
        $this->assertFalse(isset($map[$obj1]));

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);
    }

    public function testDataRemoved()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map->attach($obj1, $inf1);

        $obj2 = new stdClass();
        $inf2 = new stdClass();
        $map->attach($obj2, $inf2);

        $this->assertCount(2, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1, $map[$obj1]);

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);

        $obj1 = null;

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);
    }

    public function testGetInfo()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map->attach($obj1, $inf1);

        $obj2 = new stdClass();
        $inf2 = new stdClass();
        $map->attach($obj2, $inf2);

        $this->assertCount(2, $map);

        $map->rewind();
        $this->assertSame($inf1, $map->getInfo());

        $map->next();
        $this->assertSame($inf2, $map->getInfo());
    }

    public function testSetInfo()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map->attach($obj1, $inf1);

        $obj2 = new stdClass();
        $inf2 = new stdClass();
        $map->attach($obj2, $inf2);

        $this->assertCount(2, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1, $map[$obj1]);

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);

        $inf1_new = new stdClass();

        $map->rewind();
        $map->setInfo($inf1_new);

        $this->assertCount(2, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1_new, $map[$obj1]);

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);

        $inf1_new = null;

        $this->assertCount(1, $map);
        $this->assertFalse($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));
    }

    public function testSetInfoWithNoCurrent()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map->attach($obj1, $inf1);

        $this->assertCount(1, $map);

        $inf1_new = new stdClass();

        $map->setInfo($inf1_new);

        $this->assertCount(1, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1, $map[$obj1]);
    }

    public function testSetInfoNotObjectOnNonexistentCurrent()
    {
        $map = new WeakKeyValueMap();
        $map->setInfo(null);

        $this->assertTrue(true);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage WeakKeyValueMap expects data to be object, NULL given
     */
    public function testSetInfoNotObject()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map->attach($obj1, $inf1);

        $map->rewind();
        $map->setInfo(null);
    }

    public function testOffsetSet()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map[$obj1] = $inf1;

        $obj2 = new stdClass();
        $inf2 = new stdClass();
        $map[$obj2] = $inf2;

        $this->assertCount(2, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1, $map[$obj1]);

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);

        $inf1_new = new stdClass();

        $map[$obj1] = $inf1_new;

        $this->assertCount(2, $map);

        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1_new, $map[$obj1]);

        $this->assertTrue($map->contains($obj2));
        $this->assertTrue(isset($map[$obj2]));
        $this->assertSame($inf2, $map[$obj2]);

        $inf1_new = null;

        $this->assertCount(1, $map);
        $this->assertFalse($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage WeakKeyValueMap expects data to be object, string given
     */
    public function testOffsetSetWhenNotObject()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();

        $map[$obj1] = 'should fail';
    }

    public function testOffsetSetAttachs()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new stdClass();
        $inf1 = new stdClass();
        $map[$obj1] = $inf1;

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertTrue(isset($map[$obj1]));
        $this->assertSame($inf1, $map[$obj1]);

    }

    public function testGetCurrent()
    {
        $map = new WeakKeyValueMap();

        $obj1 = new class
        {
            function __destruct()
            {
                throw new \Exception('Prevent weakref handler to be executed');
            }
        };

        $inf1 = new stdClass();

        $obj2 = new stdClass();
        $inf2 = new stdClass();

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
        } catch (\Throwable $e) {
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
        $map = new WeakKeyValueMap();
        $storage = new SplObjectStorage();

        $obj1 = new stdClass();
        $inf1 = new stdClass();

        $obj2 = new stdClass();
        $inf2 = new stdClass();

        $storage->attach($obj1, $inf1);
        $storage->attach($obj2, $inf2);

        $this->assertCount(2, $storage);

        $map->addAll($storage);

        $this->assertCount(2, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));

        $storage = null;
        $inf1 = null;

        $this->assertCount(1, $map);
        $this->assertFalse($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));

        $obj2 = null;

        $this->assertCount(0, $map);
    }

    public function testRemoveAll()
    {
        $map = new WeakKeyValueMap();
        $storage = new SplObjectStorage();

        $obj1 = new stdClass();
        $inf1 = new stdClass();

        $obj2 = new stdClass();
        $inf2 = new stdClass();

        $storage->attach($obj1);

        $this->assertCount(1, $storage);

        $map->attach($obj1, $inf1);
        $map->attach($obj2, $inf2);
        $this->assertCount(2, $map);

        $map->removeAll($storage);

        $this->assertCount(1, $map);
        $this->assertFalse($map->contains($obj1));
        $this->assertTrue($map->contains($obj2));
    }

    public function testRemoveAllExcept()
    {
        $map = new WeakKeyValueMap();
        $storage = new SplObjectStorage();

        $obj1 = new stdClass();
        $inf1 = new stdClass();

        $obj2 = new stdClass();
        $inf2 = new stdClass();

        $storage->attach($obj1);

        $this->assertCount(1, $storage);

        $map->attach($obj1, $inf1);
        $map->attach($obj2, $inf2);
        $this->assertCount(2, $map);

        $map->removeAllExcept($storage);

        $this->assertCount(1, $map);
        $this->assertTrue($map->contains($obj1));
        $this->assertFalse($map->contains($obj2));
    }
}
