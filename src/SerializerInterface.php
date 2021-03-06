<?php

namespace Fabstract\Component\Storage;

interface SerializerInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data);

    /**
     * @param string $data
     * @return mixed
     */
    public function deserialize($data);
}
