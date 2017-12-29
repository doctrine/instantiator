<?php

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;

/**
 * A simple asset for an abstract class
 */
class UnserializeExceptionArrayObjectAsset extends ArrayObject
{
    /**
     * {@inheritDoc}
     */
    public function __wakeup()
    {
        throw new BadMethodCallException();
    }
}
