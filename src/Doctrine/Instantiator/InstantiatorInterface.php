<?php

namespace Doctrine\Instantiator;

/**
 * Instantiator provides utility methods to build objects without invoking their constructors
 */
interface InstantiatorInterface
{
    /**
     * @param string $className
     *
     * @return object
     *
     * @throws \Doctrine\Instantiator\Exception\ExceptionInterface
     */
    public function instantiate($className);
}
