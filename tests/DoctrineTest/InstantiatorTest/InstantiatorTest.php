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

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Doctrine\Instantiator\Instantiator;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

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

        $this->setExpectedException('Doctrine\\Instantiator\\Exception\\UnexpectedValueException');

        $this->instantiator->instantiate(\PDORow::class);
    }

    /**
     * @param string $invalidClassName
     *
     * @dataProvider getInvalidClassNames
     */
    public function testInstantiationFromNonExistingClass($invalidClassName)
    {
        $this->setExpectedException('Doctrine\\Instantiator\\Exception\\InvalidArgumentException');

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
            array('stdClass'),
            array(__CLASS__),
            array('Doctrine\\Instantiator\\Instantiator'),
            array('Exception'),
            array('PharException'),
            array('DoctrineTest\\InstantiatorTestAsset\\SimpleSerializableAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\ExceptionAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\FinalExceptionAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\PharExceptionAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\UnCloneableAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\XMLReaderAsset'),
            array('PharException'),
            array('ArrayObject'),
            array('DoctrineTest\\InstantiatorTestAsset\\ArrayObjectAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\WakeUpNoticesAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\UnserializeExceptionArrayObjectAsset'),
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
            array('Doctrine\\Instantiator\\InstantiatorInterface'),
            array('DoctrineTest\\InstantiatorTestAsset\\AbstractClassAsset'),
            array('DoctrineTest\\InstantiatorTestAsset\\SimpleTraitAsset')
        );
    }
}
