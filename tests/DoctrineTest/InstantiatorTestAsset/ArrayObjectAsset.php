<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use BadMethodCallException;

/**
 * Test asset that extends an internal PHP class
 *
 * @template TValue
 * @template-extends ArrayObject<int, TValue>
 */
class ArrayObjectAsset extends ArrayObject
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
}
