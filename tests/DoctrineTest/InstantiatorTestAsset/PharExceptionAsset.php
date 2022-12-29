<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use BadMethodCallException;
use PharException;

/**
 * Test asset that extends an internal PHP class
 * This class should be serializable without problems
 * and without getting the "Erroneous data format for unserializing"
 * error
 */
class PharExceptionAsset extends PharException
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
