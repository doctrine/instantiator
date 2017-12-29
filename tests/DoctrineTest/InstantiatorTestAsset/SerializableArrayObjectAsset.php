<?php

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;
use Serializable;

/**
 * Serializable test asset that also extends an internal class
 */
class SerializableArrayObjectAsset extends ArrayObject implements Serializable
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
