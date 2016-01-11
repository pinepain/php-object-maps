<?php

/*
 * This file is part of the pinepain/php-weak PHP extension.
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

use Weak\Reference;
use Weak\HashedReference;
use Weak\SplObjectStorage;

use stdClass;

class SplObjectStorageTest extends PHPUnit_Framework_TestCase
{
    public function testGetHash()
    {
        $s = new SplObjectStorage();

        $obj = new stdClass();

        // we need this to update referent object handlers HashTable so it changes it value and gives us new hash, now consistent
        new Reference($obj);

        $expected_hash = spl_object_hash($obj);

        $this->assertSame($expected_hash, $s->getHash($obj));

        $wr = new HashedReference($obj);

        $this->assertSame($expected_hash, $s->getHash($wr));

        $obj = null;

        $this->assertNull($wr->get());
        $this->assertSame($expected_hash, $s->getHash($wr));
    }

    public function testAttach()
    {
        $s = new SplObjectStorage();

        $obj1 = new stdClass();
        $s->attach($obj1);

        $this->assertCount(1, $s);
        $this->assertTrue($s->contains($obj1));
        $this->assertTrue(isset($s[$obj1]));
        $this->assertNull($s[$obj1]);

        $obj2 = new stdClass();
        $s->attach($obj2, 'data');

        $this->assertCount(2, $s);
        $this->assertTrue($s->contains($obj2));
        $this->assertTrue(isset($s[$obj2]));
        $this->assertSame($s[$obj2], 'data');
    }

    public function testObjectRemoved()
    {
        $s = new SplObjectStorage();

        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $s->attach($obj1, '$obj1');
        $s->attach($obj2, '$obj2');

        $this->assertCount(2, $s);
        $s->rewind();
        $this->assertSame($s->current(), $obj1);
        $this->assertSame('$obj1', $s->getInfo());
        $s->next();
        $this->assertSame($s->current(), $obj2);
        $this->assertSame('$obj2', $s->getInfo());

        $obj1 = null;

        $this->assertCount(1, $s);
        $s->rewind();
        $this->assertSame($s->current(), $obj2);
        $this->assertSame('$obj2', $s->getInfo());
    }


    public function testGetCurrent()
    {
        $s = new SplObjectStorage();

        $obj1 = new class
        {
            function __destruct()
            {
                throw new \Exception('Prevent weakref handler to be executed');
            }
        };

        $obj2 = new stdClass();

        $s->attach($obj1, '$obj1');
        $s->attach($obj2, '$obj2');

        $this->assertCount(2, $s);
        $s->rewind();
        $this->assertSame($s->current(), $obj1);
        $this->assertSame('$obj1', $s->getInfo());
        $s->next();
        $this->assertSame($s->current(), $obj2);
        $this->assertSame('$obj2', $s->getInfo());

        try {
            $obj1 = null;
        } catch (\Exception $e) {
            $this->assertSame('Prevent weakref handler to be executed', $e->getMessage());
        }

        $this->assertCount(2, $s);
        $s->rewind();
        $this->assertNull($s->current());
        $this->assertSame('$obj1', $s->getInfo());
        $s->next();
        $this->assertSame($s->current(), $obj2);
        $this->assertSame('$obj2', $s->getInfo());
    }
}
