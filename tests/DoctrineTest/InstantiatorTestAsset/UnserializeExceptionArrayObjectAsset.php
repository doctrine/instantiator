<?php
namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;

/**
 * A simple asset for an abstract class
 *
 * @author Marco Pivetta <ocramius@gmail.com>
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
