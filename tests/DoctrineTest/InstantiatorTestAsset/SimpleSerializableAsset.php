<?php

namespace DoctrineTest\InstantiatorTestAsset;

use BadMethodCallException;
use Serializable;

/**
 * Base serializable test asset
 */
class SimpleSerializableAsset implements Serializable
{
    /**
     * Constructor - should not be called
     *
     * @throws BadMethodCallException
     */
    public function __construct()
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     *
     * Should not be called
     *
     * @throws BadMethodCallException
     */
    public function unserialize($serialized)
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }
}
