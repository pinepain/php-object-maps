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


use Pinepain\ObjectMaps\Exceptions\OutOfBoundsException;
use Pinepain\ObjectMaps\Exceptions\OverflowException;


class ObjectBiMap implements ObjectBiMapInterface
{
    protected $behavior = self::DEFAULT;

    /**
     * @var ObjectMapInterface
     */
    protected $keys;
    /**
     * @var ObjectMapInterface
     */
    protected $values;

    /**
     * @param int $behavior
     */
    public function __construct(int $behavior = self::DEFAULT)
    {
        $key_behavior   = 0;
        $value_behavior = 0;

        if ($behavior & self::WEAK_KEY) {
            $key_behavior   |= self::WEAK_KEY;
            $value_behavior |= self::WEAK_VALUE;
        }

        if ($behavior & self::WEAK_VALUE) {
            $key_behavior   |= self::WEAK_VALUE;
            $value_behavior |= self::WEAK_KEY;
        }

        $this->keys   = new ObjectMap($key_behavior);
        $this->values = new ObjectMap($value_behavior);

        $this->behavior = $behavior;
    }

    /**
     * {@inheritdoc}
     */
    public function values(): ObjectBiMapInterface
    {
        $new_behavior = 0;

        if ($this->behavior & self::WEAK_KEY) {
            $new_behavior |= self::WEAK_VALUE;
        }

        if ($this->behavior & self::WEAK_VALUE) {
            $new_behavior |= self::WEAK_KEY;
        }

        $new = new static($new_behavior);

        $new->keys   = $this->values;
        $new->values = $this->keys;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function put(object $key, object $value)
    {
        if ($this->keys->has($key)) {
            throw new OverflowException('Value with such key already exists');
        }

        if ($this->values->has($value)) {
            // UNEXPECTED
            throw new OverflowException('Key with such value already exists');
        }

        $this->keys->put($key, $value);
        $this->values->put($value, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function get(object $key)
    {
        return $this->keys->get($key);
    }

    /**
     * {@inheritdoc}
     */

    public function has(object $key): bool
    {
        return $this->keys->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(object $key)
    {
        if (!$this->keys->has($key)) {
            throw new OutOfBoundsException('Value with such key not found');
        }

        $value = $this->keys->remove($key);

        if (!$this->values->has($value)) {
            // UNEXPECTED
            throw new OutOfBoundsException('Key with such value not found');
        }

        $this->values->remove($value);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->keys->clear();
        $this->values->clear();
    }
}
