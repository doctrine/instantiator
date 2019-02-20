<?php

namespace DoctrineTest\InstantiatorTest;

use ArrayObject;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Doctrine\Instantiator\Instantiator;
use Doctrine\Instantiator\InstantiatorInterface;
use DoctrineTest\InstantiatorTestAsset\AbstractClassAsset;
use DoctrineTest\InstantiatorTestAsset\ArrayObjectAsset;
use DoctrineTest\InstantiatorTestAsset\ExceptionAsset;
use DoctrineTest\InstantiatorTestAsset\FinalExceptionAsset;
use DoctrineTest\InstantiatorTestAsset\PharExceptionAsset;
use DoctrineTest\InstantiatorTestAsset\SerializableArrayObjectAsset;
use DoctrineTest\InstantiatorTestAsset\SimpleSerializableAsset;
use DoctrineTest\InstantiatorTestAsset\SimpleTraitAsset;
use DoctrineTest\InstantiatorTestAsset\UnCloneableAsset;
use DoctrineTest\InstantiatorTestAsset\UnserializeExceptionArrayObjectAsset;
use DoctrineTest\InstantiatorTestAsset\WakeUpNoticesAsset;
use DoctrineTest\InstantiatorTestAsset\XMLReaderAsset;
use Exception;
use PDORow;
use PharException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function str_replace;
use function uniqid;

/**
 * Tests for {@see \Doctrine\Instantiator\Instantiator}
 *
 * @covers \Doctrine\Instantiator\Instantiator
 */
class InstantiatorTest extends TestCase
{
    /** @var Instantiator */
    private $instantiator;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->instantiator = new Instantiator();
    }

    /**
     * @dataProvider getInstantiableClasses
     */
    public function testCanInstantiate(string $className) : void
    {
        self::assertInstanceOf($className, $this->instantiator->instantiate($className));
    }

    /**
     * @dataProvider getInstantiableClasses
     */
    public function testInstantiatesSeparateInstances(string $className) : void
    {
        $instance1 = $this->instantiator->instantiate($className);
        $instance2 = $this->instantiator->instantiate($className);

        self::assertEquals($instance1, $instance2);
        self::assertNotSame($instance1, $instance2);
    }

    public function testExceptionOnUnSerializationException() : void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->instantiator->instantiate(PDORow::class);
    }

    /**
     * @dataProvider getInvalidClassNames
     */
    public function testInstantiationFromNonExistingClass(string $invalidClassName) : void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instantiator->instantiate($invalidClassName);
    }

    public function testInstancesAreNotCloned() : void
    {
        $className = 'TemporaryClass' . str_replace('.', '', uniqid('', true));

        eval('namespace ' . __NAMESPACE__ . '; class ' . $className . '{}');

        $instance = $this->instantiator->instantiate(__NAMESPACE__ . '\\' . $className);

        $instance->foo = 'bar';

        $instance2 = $this->instantiator->instantiate(__NAMESPACE__ . '\\' . $className);

        self::assertObjectNotHasAttribute('foo', $instance2);
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInstantiableClasses() : array
    {
        return [
            [stdClass::class],
            [self::class],
            [Instantiator::class],
            [Exception::class],
            [PharException::class],
            [SimpleSerializableAsset::class],
            [ExceptionAsset::class],
            [FinalExceptionAsset::class],
            [PharExceptionAsset::class],
            [UnCloneableAsset::class],
            [XMLReaderAsset::class],
            [PharException::class],
            [ArrayObject::class],
            [ArrayObjectAsset::class],
            [SerializableArrayObjectAsset::class],
            [WakeUpNoticesAsset::class],
            [UnserializeExceptionArrayObjectAsset::class],
        ];
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInvalidClassNames() : array
    {
        return [
            [self::class . str_replace('.', '', uniqid('', true))],
            [InstantiatorInterface::class],
            [AbstractClassAsset::class],
            [SimpleTraitAsset::class],
        ];
    }
}
