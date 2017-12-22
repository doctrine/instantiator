<?php
namespace DoctrineTest\InstantiatorTestAsset;

use BadMethodCallException;
use Phar;

/**
 * Test asset that extends an internal PHP class
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class PharAsset extends Phar
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
