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


namespace Pinepain\ObjectMaps;

/**
 * @internal
 */
final class Bucket
{
    /**
     * @var object
     */
    public $key;
    /**
     * @var object
     */
    public $value;

    /**
     * @param object $key
     * @param object $value
     */
    public function __construct(object $key, object $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }
}
