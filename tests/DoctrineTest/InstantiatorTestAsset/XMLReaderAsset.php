<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use BadMethodCallException;
use XMLReader;

/**
 * Test asset that extends an internal PHP class
 */
class XMLReaderAsset extends XMLReader
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
