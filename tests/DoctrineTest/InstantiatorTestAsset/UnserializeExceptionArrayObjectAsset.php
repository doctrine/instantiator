<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;

/**
 * A simple asset for an abstract class
 *
 * @template TValue
 * @template-extends ArrayObject<int, TValue>
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
