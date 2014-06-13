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
 * Instantiator provides utility methods to build objects without invoking their constructors
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class Instantiator
{
    /**
     * @var Closure[]
     */
    private $cachedInstantiators = array();

    /**
     * @param string $className
     *
     * @return object
     */
    public function instantiate($className)
    {
        if (isset($this->cachedInstantiators[$className])) {
            $instantiator = $this->cachedInstantiators[$className];

            return $instantiator();
        }

        $reflectionClass = new ReflectionClass($className);

        if (\PHP_VERSION_ID >= 50400 && ! $this->hasInternalAncestors($reflectionClass)) {
            return $this->storeAndExecuteInstantiator(
                $className,
                function () use ($reflectionClass) {
                    return $reflectionClass->newInstanceWithoutConstructor();
                }
            );
        }

        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($className), $className));
    }

    /**
     * Store the instantiator in the local cache, then run it
     *
     * @param string  $className
     * @param Closure $instantiator
     *
     * @return object
     */
    private function storeAndExecuteInstantiator($className, Closure $instantiator)
    {
        $this->cachedInstantiators[$className] = $instantiator;

        return $instantiator();
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
}
