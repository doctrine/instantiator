<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

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
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Tests for {@see \Doctrine\Instantiator\Instantiator}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Doctrine\Instantiator\Instantiator
 */
class InstantiatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Instantiator
     */
    private $instantiator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->instantiator = new Instantiator();
    }

    /**
     * @param string $className
     *
     * @dataProvider getInstantiableClasses
     */
    public function testCanInstantiate($className)
    {
        $this->assertInstanceOf($className, $this->instantiator->instantiate($className));
    }

    /**
     * @param string $className
     *
     * @dataProvider getInstantiableClasses
     */
    public function testInstantiatesSeparateInstances($className)
    {
        $instance1 = $this->instantiator->instantiate($className);
        $instance2 = $this->instantiator->instantiate($className);

        $this->assertEquals($instance1, $instance2);
        $this->assertNotSame($instance1, $instance2);
    }

    public function testExceptionOnUnSerializationException()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped(
                'As of facebook/hhvm#3432, HHVM has no PDORow, and therefore '
                . ' no internal final classes that cannot be instantiated'
            );
        }

        $this->setExpectedException(UnexpectedValueException::class);

        $this->instantiator->instantiate(PDORow::class);
    }

    /**
     * @param string $invalidClassName
     *
     * @dataProvider getInvalidClassNames
     */
    public function testInstantiationFromNonExistingClass($invalidClassName)
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->instantiator->instantiate($invalidClassName);
    }

    public function testInstancesAreNotCloned()
    {
        $className = 'TemporaryClass' . str_replace('.', '', uniqid('', true));

        eval('namespace ' . __NAMESPACE__ . '; class ' . $className . '{}');

        $instance = $this->instantiator->instantiate(__NAMESPACE__ . '\\' . $className);

        $instance->foo = 'bar';

        $instance2 = $this->instantiator->instantiate(__NAMESPACE__ . '\\' . $className);

        $this->assertObjectNotHasAttribute('foo', $instance2);
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInstantiableClasses()
    {
        return array(
            array(stdClass::class),
            array(__CLASS__),
            array(Instantiator::class),
            array(Exception::class),
            array(PharException::class),
            array(SimpleSerializableAsset::class),
            array(ExceptionAsset::class),
            array(FinalExceptionAsset::class),
            array(PharExceptionAsset::class),
            array(UnCloneableAsset::class),
            array(XMLReaderAsset::class),
            array(PharException::class),
            array(ArrayObject::class),
            array(ArrayObjectAsset::class),
            array(SerializableArrayObjectAsset::class),
            array(WakeUpNoticesAsset::class),
            array(UnserializeExceptionArrayObjectAsset::class),
        );
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInvalidClassNames()
    {
        return array(
            array(__CLASS__ . str_replace('.', '', uniqid('', true))),
            array(InstantiatorInterface::class),
            array(AbstractClassAsset::class),
            array(SimpleTraitAsset::class)
        );
    }
}
