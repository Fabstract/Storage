<?php

namespace Fabstract\Component\Storage\Redis;

class RedisConfigModel
{
    /** @var string */
    public $host = 'localhost';
    /** @var int */
    public $port = 6379;
    /** @var bool */
    public $persistent = false;
    /** @var string */
    public $auth = null;
    /** @var int */
    public $index = 0;
    /** @var string */
    public $prefix = null;
    /** @var int */
    public $lifetime = 0;
}
