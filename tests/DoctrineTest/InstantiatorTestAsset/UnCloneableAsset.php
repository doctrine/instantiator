<?php
namespace DoctrineTest\InstantiatorTestAsset;

use BadMethodCallException;

/**
 * Base un-cloneable asset
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class UnCloneableAsset
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
     * Magic `__clone` - should not be invoked
     *
     * @throws BadMethodCallException
     */
    public function __clone()
    {
        throw new BadMethodCallException('Not supposed to be called!');
    }
}
