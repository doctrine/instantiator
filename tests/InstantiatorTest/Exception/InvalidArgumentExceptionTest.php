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

namespace InstantiatorTest\Exception;

use Exception;
use Instantiator\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Tests for {@see \Instantiator\Exception\InvalidArgumentException}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Instantiator\Exception\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testFromSerializationTriggeredException()
    {
        $reflectionClass = new ReflectionClass($this);
        $previous        = new Exception();
        $exception       = InvalidArgumentException::fromSerializationTriggeredException($reflectionClass, $previous);

        $this->assertInstanceOf('Instantiator\\Exception\\InvalidArgumentException', $exception);
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame(
            'An exception was raised while trying to instantiate an instance of '
            . __CLASS__  . ' via un-serialization',
            $exception->getMessage()
        );
    }

    public function testFromNonExistingTypeWithNonExistingClass()
    {
        $className = __CLASS__ . uniqid();
        $exception = InvalidArgumentException::fromNonExistingType($className);

        $this->assertInstanceOf('Instantiator\\Exception\\InvalidArgumentException', $exception);
        $this->assertSame('The provided class "' . $className . '" does not exist', $exception->getMessage());
    }
}
