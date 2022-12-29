<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use BadMethodCallException;
use Exception;

/**
 * Test asset that extends an internal PHP base exception
 */
class ExceptionAsset extends Exception
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
