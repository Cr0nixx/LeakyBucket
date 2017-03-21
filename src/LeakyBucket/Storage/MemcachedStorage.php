<?php

/*
 * Copyright (c) Philip Skuza <philip.skuza@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LeakyBucket\Storage;

/**
 * Storage for Memcached servers.
 *
 * @author Philip Skuza <philip.skuza@gmail.com>
 */
class MemcachedStorage implements StorageInterface
{
    /**
     * An instance of the MemcachedClient
     *
     * @var MemcachedClient
     */
    private static $mc;

    /**
     * Class constructor.
     *
     * @param MemcachedClient $mc An instance of the MemcachedClient
     */
    public function __construct($mc = null)
    {
        if ($mc) {
            self::$mc = $mc;
        } elseif (!self::$mc) {
            self::$mc = new Memcached();
			self::$mc->addServer(127.0.0.1, 11211, 1);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store($key, $value, $ttl = 0)
    {
        if (isset($ttl) && $ttl > 0) {
            self::$mc->set($key, serialize($value), $ttl);
        } else {
            self::$mc->set($key, serialize($value));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return unserialize(self::$mc->get($key));
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        self::$mc->get($key);
		if (self::$mc->getResultCode() === Memcached::RES_NOTFOUND) {
			return false;
		}
		return true;
    }

    /**
     * {@inheritdoc}
     */
    public function purge($key)
    {
        self::$mc->delete($key);
    }
}
