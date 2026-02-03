<?php

declare(strict_types=1);

namespace Doctrine\Instantiator;

use Doctrine\Instantiator\Exception\ExceptionInterface;

/**
 * Instantiator provides utility methods to build objects without invoking their constructors
 */
interface InstantiatorInterface
{
    /**
     * @phpstan-param class-string<T> $className
     *
     * @phpstan-return T
     *
     * @throws ExceptionInterface
     *
     * @template T of object
     */
    public function instantiate(string $className): object;
}
