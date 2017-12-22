<?php
namespace Doctrine\Instantiator;

/**
 * Instantiator provides utility methods to build objects without invoking their constructors
 *
 * @author Marco Pivetta <ocramius@gmail.com>
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
