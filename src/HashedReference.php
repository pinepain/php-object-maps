<?php

/*
 * This file is part of the pinepain/php-ref-lib PHP library.
 *
 * Copyright (c) 2016 Bogdan Padalko <pinepain@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit http://opensource.org/licenses/MIT
 */

namespace Ref;

use function spl_object_hash;

class HashedReference extends WeakReference
{
    private $hash;

    public function __construct($referent, callable $notify = null, callable $hash_function = null)
    {
        parent::__construct($referent, $notify);

        $this->hash = $hash_function ? $hash_function($referent) : spl_object_hash($referent);
    }

    public function getHash() : string
    {
        return $this->hash;
    }
}
