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

namespace Instantiator;

use Closure;
use ReflectionClass;

/**
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
final class Instantiator
{
    /**
     * @var Closure[]
     */
    private $cachedInstantiators = array();

    /**
     * @var object[]
     */
    private $cachedCloneables = array();

    /**
     * {@inheritDoc}
     */
    public function instantiate($className)
    {
        if (isset($this->cachedCloneables[$className])) {
            return clone $this->cachedCloneables[$className];
        }

        if (isset($this->cachedInstantiators[$className])) {
            $instantiator = $this->cachedInstantiators[$className];

            return $instantiator();
        }

        return $this->buildInstance($className);
    }

    /**
     * @param string $className
     *
     * @return object
     */
    private function buildInstance($className)
    {
        $reflectionClass = new ReflectionClass($className);
        $cloneable       = ! $reflectionClass->hasMethod('__clone');

        if (\PHP_VERSION_ID >= 50400 && ! $this->hasInternalAncestors($reflectionClass)) {
            return $this->storeAndExecuteInstantiator(
                $cloneable,
                $className,
                function () use ($reflectionClass) {
                    return $reflectionClass->newInstanceWithoutConstructor();
                }
            );
        }

        $serializationFormat = 'O';

        if (
            $this->isUnserializeIncompatiblePhpVersion()
            && $reflectionClass->implementsInterface('Serializable')
        ) {
            $serializationFormat = 'C';
        }

        $serializedString = sprintf(
            '%s:%d:"%s":0:{}',
            $serializationFormat,
            strlen($className),
            $className
        );

        return $this->storeAndExecuteInstantiator(
            $cloneable,
            $className,
            function () use ($serializedString) {
                return unserialize($serializedString);
            }
        );
    }

    /**
     * Store the instantiator in the local cache, then run it
     *
     * @param bool    $cloneable
     * @param string  $className
     * @param Closure $instantiator
     *
     * @return object
     */
    private function storeAndExecuteInstantiator($cloneable, $className, Closure $instantiator)
    {
        $this->cachedInstantiators[$className] = $instantiator;

        $instance = $instantiator();

        if ($cloneable) {
            $this->cachedCloneables[$className] = $instance;

            return clone $this->cachedCloneables[$className];
        }

        return $instance;
    }

    /**
     * Verifies whether the given class is to be considered internal
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return bool
     */
    private function hasInternalAncestors(ReflectionClass $reflectionClass)
    {
        do {
            if ($reflectionClass->isInternal()) {
                return true;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return false;
    }

    /**
     * Verifies if the given PHP version implements the `Serializable` interface serialization
     * with an incompatible serialization format
     *
     * @return bool
     */
    private function isUnserializeIncompatiblePhpVersion()
    {
        return PHP_VERSION_ID === 50429 || PHP_VERSION_ID === 50513 || PHP_VERSION_ID === 50600;
    }
}
