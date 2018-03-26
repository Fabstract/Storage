<?php

namespace Fabstract\Component\Storage;

interface StorageInterface
{
    /**
     * @param string|int $key
     * @param string $value
     * @param int $lifetime
     * @return bool
     */
    public function set($key, $value, $lifetime = 0);

    /**
     * @param string|int $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string|int $key
     * @return bool
     */
    public function delete($key);

    /**
     * @param string|int $key
     * @return bool
     */
    public function exists($key);

    /**
     * @return SerializerInterface
     */
    public function getSerializer();
}
