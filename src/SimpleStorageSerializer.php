<?php

namespace Fabstract\Component\Storage;

use Fabstract\Component\Assert\Assert;

class SimpleStorageSerializer implements SerializerInterface
{

    /**
     * @param string $data
     * @return string
     */
    public function serialize($data)
    {
        Assert::isString($data, 'data');

        return $data;
    }

    /**
     * @param string $data
     * @return string
     */
    public function deserialize($data)
    {
        Assert::isString($data, 'data');

        return $data;
    }
}
