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
 * and is licensed under the MIT license.
 */

namespace InstantiatorTest;

use Instantiator\Instantiator;
use PHPUnit_Framework_TestCase;

/**
 * Tests for {@see \Instantiator\Instantiator}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Instantiator\Instantiator
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
        if (! (\PHP_VERSION_ID === 50429 || \PHP_VERSION_ID === 50513)) {
            $this->markTestSkipped('This test requires PHP 5.4.29 or 5.5.13 to run');
        }

        $this->setExpectedException('Instantiator\\Exception\\InvalidArgumentException');

        $this->instantiator->instantiate('InstantiatorTestAsset\\SerializableArrayObjectAsset');
    }

    /**
     * @param string $invalidClassName
     *
     * @dataProvider getInvalidClassNames
     */
    public function testInstantiationFromNonExistingClass($invalidClassName)
    {
        $this->setExpectedException('Instantiator\\Exception\\InvalidArgumentException');

        $this->instantiator->instantiate($invalidClassName);
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInstantiableClasses()
    {
        if (\PHP_VERSION_ID === 50429 || \PHP_VERSION_ID === 50513) {
            return array(
                array('stdClass'),
                array(__CLASS__),
                array('Instantiator\\Instantiator'),
                array('PharException'),
                array('InstantiatorTestAsset\\SimpleSerializableAsset'),
                array('InstantiatorTestAsset\\PharExceptionAsset'),
                array('InstantiatorTestAsset\\UnCloneableAsset'),
            );
        }

        return array(
            array('stdClass'),
            array(__CLASS__),
            array('Instantiator\\Instantiator'),
            array('PharException'),
            array('ArrayObject'),
            array('InstantiatorTestAsset\\SimpleSerializableAsset'),
            array('InstantiatorTestAsset\\ArrayObjectAsset'),
            array('InstantiatorTestAsset\\PharExceptionAsset'),
            array('InstantiatorTestAsset\\SerializableArrayObjectAsset'),
            array('InstantiatorTestAsset\\UnCloneableAsset'),
        );
    }


    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInvalidClassNames()
    {
        $classNames = array(
            array(__CLASS__ . uniqid()),
            array('Instantiator\\InstantiatorInterface'),
            array('InstantiatorTestAsset\\AbstractClassAsset'),
        );

        if (\PHP_VERSION_ID >= 50400) {
            $classNames[] = array('InstantiatorTestAsset\\SimpleTraitAsset');
        }

        return $classNames;
    }
}
