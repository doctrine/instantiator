<?php

namespace DoctrineTest\InstantiatorTest\Exception;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\Instantiator\InstantiatorInterface;
use DoctrineTest\InstantiatorTestAsset\AbstractClassAsset;
use DoctrineTest\InstantiatorTestAsset\SimpleTraitAsset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function sprintf;
use function str_replace;
use function uniqid;

/**
 * Tests for {@see \Doctrine\Instantiator\Exception\InvalidArgumentException}
 *
 * @covers \Doctrine\Instantiator\Exception\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends TestCase
{
    public function testFromNonExistingTypeWithNonExistingClass() : void
    {
        $className = self::class . str_replace('.', '', uniqid('', true));
        $exception = InvalidArgumentException::fromNonExistingClass($className);

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
        self::assertSame('The provided class "' . $className . '" does not exist', $exception->getMessage());
    }

    public function testFromNonExistingTypeWithTrait() : void
    {
        $exception = InvalidArgumentException::fromNonExistingClass(SimpleTraitAsset::class);

        self::assertSame(
            sprintf('The provided type "%s" is a trait, and can not be instantiated', SimpleTraitAsset::class),
            $exception->getMessage()
        );
    }

    public function testFromNonExistingTypeWithInterface() : void
    {
        $exception = InvalidArgumentException::fromNonExistingClass(InstantiatorInterface::class);

        self::assertSame(
            sprintf(
                'The provided type "%s" is an interface, and can not be instantiated',
                InstantiatorInterface::class
            ),
            $exception->getMessage()
        );
    }

    public function testFromAbstractClass() : void
    {
        $reflection = new ReflectionClass(AbstractClassAsset::class);
        $exception  = InvalidArgumentException::fromAbstractClass($reflection);

        self::assertSame(
            sprintf(
                'The provided class "%s" is abstract, and can not be instantiated',
                AbstractClassAsset::class
            ),
            $exception->getMessage()
        );
    }
}
