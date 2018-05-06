<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace Fabstract\Component\Storage\Redis;

use Fabstract\Component\Assert\Assert;
use Fabstract\Component\Storage\Exception\Exception;
use Fabstract\Component\Storage\SerializerInterface;
use Fabstract\Component\Storage\SimpleStorageSerializer;
use Fabstract\Component\Storage\StorageInterface;

class RedisStorage implements StorageInterface
{
    /** @var SerializerInterface */
    private $serializer = null;
    /** @var RedisConfigModel */
    private $config = null;
    /** @var \Redis */
    private $redis = null;

    /**
     * RedisStorage constructor.
     * @param RedisConfigModel $config
     * @param SerializerInterface $serializer
     */
    public function __construct($config, $serializer = null)
    {
        Assert::isType($config, RedisConfigModel::class, 'config');
        if ($serializer !== null) {
            Assert::isImplements($serializer, SerializerInterface::class, 'serializer');
        }

        $this->config = $config;

        if ($serializer === null) {
            $this->serializer = new SimpleStorageSerializer();
        } else {
            $this->serializer = $serializer;
        }
    }

    /**
     * @param string|int $key
     * @param string $value
     * @param int $lifetime
     * @return bool
     * @throws Exception
     */
    public function set($key, $value, $lifetime = 0)
    {
        $redis = $this->getRedis();
        $serializer = $this->getSerializer();

        $content = $serializer->serialize($value);

        if ($lifetime <= 0) {
            $lifetime = $this->config->lifetime;
        }

        $key = $this->getKeyWithPrefix($key);
        if ($lifetime > 0) {
            $saved = $redis->setex($key, $lifetime, $content);
        } else {
            $saved = $redis->set($key, $content);
        }

        if (!$saved) {
            throw new Exception('Failed storing the data in redis');
        }

        return true; // return true means success
    }

    /**
     * @param string|int $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        $redis = $this->getRedis();
        $serializer = $this->getSerializer();

        $key = $this->getKeyWithPrefix($key);

        $content = $redis->get($key);

        if ($content === false) {
            return $default;
        }

        return $serializer->deserialize($content);
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function delete($key)
    {
        $redis = $this->getRedis();

        $key = $this->getKeyWithPrefix($key);

        $redis->delete($key);
        return true;
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function exists($key)
    {
        $redis = $this->getRedis();

        $key = $this->getKeyWithPrefix($key);

        return $redis->exists($key);
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return \Redis
     */
    private function getRedis()
    {
        if ($this->redis === null) {
            $redis = new \Redis();

            if ($this->config->persistent) {
                $connected = $redis->pconnect($this->config->host, $this->config->port);
            } else {
                $connected = $redis->connect($this->config->host, $this->config->port);
            }

            if (!$connected) {
                throw new Exception('Could not connect to redis server');
            }

            if ($this->config->auth !== null) {
                $authenticated = $redis->auth($this->config->auth);
                if (!$authenticated) {
                    throw new Exception('Failed to authenticate with the redis server');
                }
            }

            if ($this->config->index > 0) {
                $selected = $redis->select($this->config->index);
                if (!$selected) {
                    throw new Exception('Redis server selected database failed');
                }
            }

            $this->redis = $redis;
        }

        return $this->redis;
    }

    private function getKeyWithPrefix($key)
    {
        if ($this->config->prefix !== null && strlen($this->config->prefix) > 0) {
            $key = $this->config->prefix . $key;
        }
        return $key;
    }
}
