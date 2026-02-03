<?php

declare(strict_types=1);

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

    public function serialize(): string
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
    public function unserialize(string $serialized): void
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }

    /** @return mixed[] */
    public function __serialize(): array
    {
        return [];
    }

    /** @param mixed[] $data */
    public function __unserialize(array $data): void
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }
}
