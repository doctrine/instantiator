<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;
use Override;
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
     * Should not be called
     *
     * @throws BadMethodCallException
     */
    #[Override]
    public function unserialize(string $serialized): void
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }

    /** @param mixed[] $data */
    #[Override]
    public function __unserialize(array $data): void
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }
}
