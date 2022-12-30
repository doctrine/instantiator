<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;
use Serializable;

/**
 * Serializable test asset that also extends an internal class
 *
 * @template TValue
 * @template-extends ArrayObject<int, TValue>
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
     *
     * Should not be called
     *
     * @throws BadMethodCallException
     */
    public function unserialize($serialized): void
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }

    /** @param mixed[] $data */
    public function __unserialize(array $data): void
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }
}
