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

use Weak\HashedReference;

use stdClass;

class HashedReferenceTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $obj = new stdClass();
        $obj_hash = 'test_hash';

        $n = $this->getMock('stdClass', ['notify', 'hash']);

        $n->expects($this->once())
            ->method('notify');


        $n->expects($this->once())
            ->method('hash')
            ->willReturn($obj_hash);

        $notify = function () use (&$n) {
            return $n->notify();
        };

        $hash = function () use (&$n) {
            return $n->hash();
        };

        $wr = new HashedReference($obj, $notify, $hash);

        $this->assertSame($obj, $wr->get());
        $this->assertTrue($wr->valid());
        $this->assertSame($obj_hash, $wr->getHash());

        $obj = null;

        $this->assertNull($wr->get());
        $this->assertFalse($wr->valid());
        $this->assertSame($obj_hash, $wr->getHash());
    }
}
