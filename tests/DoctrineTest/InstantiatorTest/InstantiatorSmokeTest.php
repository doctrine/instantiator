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

use PHPUnit_Framework_TestCase;
use PHPUnit_Util_PHP;

/**
 * Smoke tests for {@see \Doctrine\Instantiator\Instantiator} - tests all declared classes against the instantiator.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @coversNothing
 */
class InstantiatorSmokeTest extends PHPUnit_Framework_TestCase
{
    private $template = <<<'PHP'
<?php

require_once %s;

try {
    $instantiator = new \Doctrine\Instantiator\Instantiator();

    $instantiator->instantiate(%s);
} catch (\Doctrine\Instantiator\Exception\ExceptionInterface $e) {
}

echo 'SUCCESS: ' . %s;
PHP;

    /**
     * @dataProvider getDeclaredClasses
     */
    public function testFoo($className)
    {
        $runner = PHPUnit_Util_PHP::factory();

        $code = sprintf(
            $this->template,
            var_export(realpath(__DIR__ . '/../../../vendor/autoload.php'), true),
            var_export($className, true),
            var_export($className, true)
        );

        $result = $runner->runJob($code);

        if (('SUCCESS: ' . $className) !== $result['stdout']) {
            $this->fail(sprintf(
                "Crashed with class '%s'.\n\nStdout:\n%s\nStderr:\n%s\nGenerated code:\n%s'",
                $className,
                $result['stdout'],
                $result['stderr'],
                $code
            ));
        }

        $this->assertSame('SUCCESS: ' . $className, $result['stdout']);
    }

    /**
     * Data Provider
     *
     * @return array[]
     */
    public function getDeclaredClasses()
    {
        return array_map(
            function ($className) {
                return array($className);
            },
            get_declared_classes()
        );
    }
}
