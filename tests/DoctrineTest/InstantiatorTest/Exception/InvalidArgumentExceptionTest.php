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

namespace DoctrineTest\InstantiatorTest\Exception;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\Instantiator\InstantiatorInterface;
use DoctrineTest\InstantiatorTestAsset\AbstractClassAsset;
use DoctrineTest\InstantiatorTestAsset\SimpleTraitAsset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for {@see \Doctrine\Instantiator\Exception\InvalidArgumentException}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Doctrine\Instantiator\Exception\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends TestCase
{
    public function testFromNonExistingTypeWithNonExistingClass() : void
    {
        $className = __CLASS__ . str_replace('.', '', uniqid('', true));
        $exception = InvalidArgumentException::fromNonExistingClass($className);

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertSame('The provided class "' . $className . '" does not exist', $exception->getMessage());
    }

    public function testFromNonExistingTypeWithTrait() : void
    {
        $exception = InvalidArgumentException::fromNonExistingClass(SimpleTraitAsset::class);

        $this->assertSame(
            sprintf('The provided type "%s" is a trait, and can not be instantiated', SimpleTraitAsset::class),
            $exception->getMessage()
        );
    }

    public function testFromNonExistingTypeWithInterface() : void
    {
        $exception = InvalidArgumentException::fromNonExistingClass(InstantiatorInterface::class);

        $this->assertSame(
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

        $this->assertSame(
            sprintf(
                'The provided class "%s" is abstract, and can not be instantiated',
                AbstractClassAsset::class
            ),
            $exception->getMessage()
        );
    }
}
