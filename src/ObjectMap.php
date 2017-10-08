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
use Ref\WeakReference;
use function spl_object_hash;


class ObjectMap implements ObjectMapInterface
{
    use ObjectTypeHintTrait;

    protected $behavior = self::DEFAULT;

    /**
     * @var Bucket[]
     */
    protected $keys = [];

    /**
     * @param int $behavior
     */
    public function __construct(int $behavior = self::DEFAULT)
    {
        $this->behavior = $behavior;
    }

    /**
     * {@inheritdoc}
     */
    public function put($key, $value)
    {
        $this->assertObject($key, 'Key');
        $this->assertObject($value, 'Value'); // while we may associate non-object value, for interface compatibility we don't do that

        $hash = $this->getHash($key);

        if (isset($this->keys[$hash])) {
            throw new OverflowException('Value with such key already exists');
        }

        $bucket = $this->createBucket($key, $value, $hash);

        $this->keys[$hash] = $bucket;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $this->assertObject($key, 'Key');

        $hash = $this->getHash($key);

        if (!isset($this->keys[$hash])) {
            throw new OutOfBoundsException('Value with such key not found');
        }

        $bucket = $this->keys[$hash];

        return $this->fetchBucketValue($bucket);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key): bool
    {
        $this->assertObject($key, 'Key');

        $hash = $this->getHash($key);

        return isset($this->keys[$hash]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $this->assertObject($key, 'Key');

        $hash = $this->getHash($key);

        if (!isset($this->keys[$hash])) {
            throw new OutOfBoundsException('Value with such key not found');
        }

        $bucket = $this->keys[$hash];

        $this->doRemove($hash);

        return $this->fetchBucketValue($bucket);
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
        $this->keys = [];
    }

    /**
     * @param object $value
     *
     * @return string
     */
    protected function getHash($value)
    {
        return spl_object_hash($value);
    }

    /**
     * @param string $hash
     *
     * @return void
     */
    protected function doRemove(string $hash)
    {
        unset($this->keys[$hash]);
    }

    /**
     * @param object $key
     * @param object $value
     * @param string $hash
     *
     * @return Bucket
     */
    protected function createBucket($key, $value, string $hash): Bucket
    {
        if ($this->behavior & self::WEAK_KEY) {
            $key = $this->createReference($key, $hash);
        }

        if ($this->behavior & self::WEAK_VALUE) {
            $value = $this->createReference($value, $hash);
        }

        return new Bucket($key, $value);
    }

    /**
     * @param Bucket $bucket
     *
     * @return null|object
     */
    protected function fetchBucketValue(Bucket $bucket)
    {
        if ($this->behavior & self::WEAK_VALUE) {
            assert($bucket->value instanceof WeakReference);

            return $bucket->value->get();
        }

        return $bucket->value;
    }

    /**
     * @param        $obj
     * @param string $hash
     *
     * @return WeakReference
     */
    protected function createReference($obj, string $hash): WeakReference
    {
        return new WeakReference($obj, function () use ($hash) {
            $this->doRemove($hash);
        });
    }
}
