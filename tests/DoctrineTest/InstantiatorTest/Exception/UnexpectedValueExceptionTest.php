<?php
namespace DoctrineTest\InstantiatorTest\Exception;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use DoctrineTest\InstantiatorTestAsset\AbstractClassAsset;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for {@see \Doctrine\Instantiator\Exception\UnexpectedValueException}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Doctrine\Instantiator\Exception\UnexpectedValueException
 */
class UnexpectedValueExceptionTest extends TestCase
{
    public function testFromSerializationTriggeredException() : void
    {
        $reflectionClass = new ReflectionClass($this);
        $previous        = new Exception();
        $exception       = UnexpectedValueException::fromSerializationTriggeredException($reflectionClass, $previous);

        $this->assertInstanceOf(UnexpectedValueException::class, $exception);
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame(
            'An exception was raised while trying to instantiate an instance of "'
            . __CLASS__  . '" via un-serialization',
            $exception->getMessage()
        );
    }

    public function testFromUncleanUnSerialization() : void
    {
        $reflection = new ReflectionClass(AbstractClassAsset::class);
        $exception  = UnexpectedValueException::fromUncleanUnSerialization($reflection, 'foo', 123, 'bar', 456);

        $this->assertInstanceOf(UnexpectedValueException::class, $exception);
        $this->assertSame(
            sprintf(
                'Could not produce an instance of "%s" '
                . 'via un-serialization, since an error was triggered in file "bar" at line "456"',
                AbstractClassAsset::class
            ),
            $exception->getMessage()
        );

        $previous = $exception->getPrevious();

        $this->assertInstanceOf('Exception', $previous);
        $this->assertSame('foo', $previous->getMessage());
        $this->assertSame(123, $previous->getCode());
    }
}
