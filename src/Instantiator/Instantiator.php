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
use LazyMap\CallbackLazyMap;
use ReflectionClass;

/**
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
final class Instantiator implements InstantiatorInterface
{
    /**
     * Markers used internally by PHP to define whether {@see \unserialize} should invoke
     * the method {@see \Serializable::unserialize()} when dealing with classes implementing
     * the {@see \Serializable} interface.
     */
    const SERIALIZATION_FORMAT_USE_UNSERIALIZER   = 'C';
    const SERIALIZATION_FORMAT_AVOID_UNSERIALIZER = 'O';

    /**
     * @var CallbackLazyMap of {@see \Closure} instances
     */
    private static $cachedInstantiators;

    /**
     * @var CallbackLazyMap of objects that can directly be cloned
     */
    private static $cachedCloneables;

    /**
     * @var bool[] map of all internal php classes that require a custom serialization format
     */
    private static $internalSerializableClasses = array(
        'Closure'                    => true,
        'Generator'                  => true,
        'SplFileInfo'                => true,
        'DirectoryIterator'          => true,
        'FilesystemIterator'         => true,
        'RecursiveDirectoryIterator' => true,
        'GlobIterator'               => true,
        'SplFileObject'              => true,
        'SplTempFileObject'          => true,
        'PDORow'                     => true,
        'SimpleXMLElement'           => true,
        'SimpleXMLIterator'          => true,
        'Phar'                       => true,
        'PharData'                   => true,
        'PharFileInfo'               => true,
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        // initialize static cached state, if not done before
        self::$cachedInstantiators = $this->getInstantiatorsMap();
        self::$cachedCloneables    = $this->getCloneablesMap();
    }

    /**
     * {@inheritDoc}
     */
    public function instantiate($className)
    {
        if ($cloneable = self::$cachedCloneables->$className) {
            return clone $cloneable;
        }

        $factory = self::$cachedInstantiators->$className;

        /* @var $factory Closure */
        return $factory();
    }

    /**
     * @internal
     * @private
     *
     * Builds a {@see \Closure} capable of instantiating the given $className without
     * invoking its constructor.
     * This method is only exposed as public because of PHP 5.3 compatibility. Do not
     * use this method in your own code
     *
     * @param string $className
     *
     * @return Closure
     */
    public function buildFactory($className)
    {
        $reflectionClass = new ReflectionClass($className);

        if (\PHP_VERSION_ID >= 50400 && ! $this->hasInternalAncestors($reflectionClass)) {
            return function () use ($reflectionClass) {
                return $reflectionClass->newInstanceWithoutConstructor();
            };
        }

        $serializationFormat = $this->getSerializationFormat($reflectionClass);
        $defaultValues = static::SERIALIZATION_FORMAT_USE_UNSERIALIZER === $serializationFormat
            ? $this->getSerializedDefaultValues($reflectionClass)
            : array();

        $serializedString = sprintf(
            '%s:%d:"%s":0:{}',
            $serializationFormat,
            strlen($className),
            $className,
            count($defaultValues),
            implode('', $defaultValues)
        );

        return function () use ($serializedString) {
            return unserialize($serializedString);
        };
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
     * Verifies whether the given class is or has an ancestor that is an internal
     * class that requires serialization
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return bool
     */
    /*private function isInternalClassWithRequiredSerializedString(ReflectionClass $reflectionClass)
    {
        do {
            if ($reflectionClass->isInternal()
                && isset(self::$internalSerializableClasses[$reflectionClass->getName()])
            ) {
                return true;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return false;
    }*/

    /**
     * Verifies if the given PHP version implements the `Serializable` interface serialization
     * with an incompatible serialization format. If that's the case, use serialization marker
     * "C" instead of "O".
     *
     * @link http://news.php.net/php.internals/74654
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return string the serialization format marker, either self::SERIALIZATION_FORMAT_USE_UNSERIALIZER
     *         or self::SERIALIZATION_FORMAT_AVOID_UNSERIALIZER
     */
    private function getSerializationFormat(ReflectionClass $reflectionClass)
    {
        if ($this->isPhpVersionWithBrokenSerializationFormat()
            && $reflectionClass->implementsInterface('Serializable')
        ) {
            return self::SERIALIZATION_FORMAT_USE_UNSERIALIZER;
        }

        return self::SERIALIZATION_FORMAT_AVOID_UNSERIALIZER;
    }

    /**
     * Checks whether the current PHP runtime uses an incompatible serialization format
     *
     * @return bool
     */
    private function isPhpVersionWithBrokenSerializationFormat()
    {
        return PHP_VERSION_ID === 50429 || PHP_VERSION_ID === 50513;
    }

    /**
     * Builds or fetches the instantiators map
     *
     * @return CallbackLazyMap
     */
    private function getInstantiatorsMap()
    {
        $that = $this; // PHP 5.3 compat

        return self::$cachedInstantiators = self::$cachedInstantiators
            ?: new CallbackLazyMap(function ($className) use ($that) {
                return $that->buildFactory($className);
            });
    }

    /**
     * Builds or fetches the cloneables map
     *
     * @return CallbackLazyMap
     */
    private function getCloneablesMap()
    {
        $cachedInstantiators = $this->getInstantiatorsMap();

        return self::$cachedCloneables = self::$cachedCloneables
            ?: new CallbackLazyMap(function ($className) use ($cachedInstantiators) {
                $reflection = new ReflectionClass($className);

                // not cloneable if it implements `__clone`
                if ($reflection->hasMethod('__clone')) {
                    return null;
                }

                /* @var $factory Closure */
                $factory = $cachedInstantiators->$className;

                return $factory();
            });
    }

    /**
     * @param ReflectionClass $reflectionClass
     *
     * @return string[]
     */
    private function getSerializedDefaultValues(ReflectionClass $reflectionClass)
    {
        $properties = array();
        $defaults   = $reflectionClass->getDefaultProperties();

        do {
            foreach ($reflectionClass->getProperties() as $property) {
                if (! $property->getDeclaringClass()->getName() === $reflectionClass->getName()) {
                    continue;
                }

                $visibility = 'public';

                if ($property->isPrivate()) {
                    $visibility = "\0" . $property->getDeclaringClass()->getName() . "\0private";
                }

                if ($property->isProtected()) {
                    $visibility = "\0*\0protected";
                }

                $defaultValue = isset($defaults[$property->getName()]) ? $defaults[$property->getName()] : null;
                $properties[] = serialize($visibility) . serialize($defaultValue);
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return $properties;
    }
}
