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

namespace Weak;

use SplObjectStorage;

class WeakKeyMap extends SplObjectStorage
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

        if ($current instanceof Reference) {
            $current = $current->get();
        }

        /* In some rare cases (it should never happens normally) orphaned weak reference may occurs in storage, so
         * so $current here MAY be null
         */

        return $current;
    }

    /** {@inheritdoc} */
    public function offsetSet($object, $data = null)
    {
        $this->attach($object, $data);
    }

    /** {@inheritdoc} */
    public function addAll($storage)
    {
        foreach ($storage as $obj) {
            $this->attach($obj, $storage[$obj]);
        }
    }

    /** {@inheritdoc} */
    public function removeAllExcept($storage)
    {
        $pending_removal = new \SplObjectStorage();

        foreach ($this as $obj) {
            if (!$storage->contains($obj)) {
                $pending_removal->attach($obj);
            }
        }

        $this->removeAll($pending_removal);
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
