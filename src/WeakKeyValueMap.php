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

use RuntimeException;
use SplObjectStorage;

class WeakKeyValueMap extends SplObjectStorage
{
    const WEAK_KEY = 1;
    const WEAK_VAL = 2;
    protected $behavior = self::WEAK_KEY | self::WEAK_VAL;

    /** {@inheritdoc} */
    public function attach($object, $data = null)
    {
        if ($this->behavior & self::WEAK_KEY) {
            $object = $this->buildWeakKey($object);
        }

        if ($this->behavior & self::WEAK_VAL) {
            $data = $this->buildWeakValue($object, $data);
        }

        parent::attach($object, $data);
    }

    /** {@inheritdoc} */
    public function current()
    {
        $object = parent::current();

        if ($this->behavior & self::WEAK_KEY) {
            $object = $this->extractWeakObject($object);
        }

        /* In some rare cases (it should never happen normally) orphaned weak reference may occurs in storage, so
         * so current object here MAY be null
         */

        return $object;
    }

    /** {@inheritdoc} */
    public function addAll($storage)
    {
        foreach ($storage as $obj) {
            $this->attach($obj, $storage[$obj]);
        }
    }

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
        if ($this->behavior & self::WEAK_KEY) {
            if ($object instanceof HashedReference) {
                return $object->getHash();
            }
        }

        return parent::getHash($object);
    }

    /** {@inheritdoc} */
    public function getInfo()
    {
        $info = parent::getInfo();

        if ($this->behavior & self::WEAK_VAL) {
            $info = $this->extractWeakObject($info);
        }

        return $info;
    }

    /** {@inheritdoc} */
    public function setInfo($data)
    {
        $object = $this->current();

        if (!$object) {
            return; // nothing to do
        }

        if ($this->behavior & self::WEAK_VAL) {
            $data = $this->buildWeakValue($object, $data);
        }

        parent::setInfo($data);
    }

    /** {@inheritdoc} */
    public function offsetSet($object, $data = null)
    {
        $this->attach($object, $data);
    }

    /** {@inheritdoc} */
    public function offsetGet($object)
    {
        $info = parent::offsetGet($object);

        if ($this->behavior & self::WEAK_VAL) {
            $info = $this->extractWeakObject($info);
        }

        return $info;
    }

    protected function validateInfo($data)
    {
        if (!is_object($data)) {
            throw new RuntimeException(static::class . ' expects data to be object, ' . gettype($data) . ' given');
        }
    }

    protected function extractWeakObject($object)
    {
        if ($object instanceof Reference) {
            $object = $object->get();
        }

        return $object;
    }

    protected function buildWeakKey($object)
    {
        $object = new HashedReference($object, [$this, 'detach'], 'spl_object_hash');

        return $object;
    }

    protected function buildWeakValue($object, $data)
    {
        $this->validateInfo($data);

        $data = new Reference($data, function () use ($object) {
            $this->detach($object);
        });

        return $data;
    }

}
