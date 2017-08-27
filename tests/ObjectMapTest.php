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


use Pinepain\ObjectMaps\ObjectMap;
use Pinepain\ObjectMaps\ObjectMapInterface;


class ObjectMapTest extends AbstractObjectMapInterfaceTest
{
    public function buildMap(int $behavior = ObjectMapInterface::DEFAULT)
    {
        return new ObjectMap($behavior);
    }
}
