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

namespace Weak;

class SplObjectStorage extends \SplObjectStorage
{
    /** {@inheritdoc} */
    public function attach($object, $data = null)
    {
        $object = new HashedReference($object, [$this, 'detach'], 'spl_object_hash');

        parent::attach($object, $data);
    }

    /** {@inheritdoc} */
    public function current()
    {
        $current = parent::current();

        if ($current) {
            $current = $current->get();
        }

        return $current;
    }

    /** {@inheritdoc} */
    public function getHash($object)
    {
        if ($object instanceof HashedReference) {
            return $object->getHash();
        }

        return parent::getHash($object);
    }
}
