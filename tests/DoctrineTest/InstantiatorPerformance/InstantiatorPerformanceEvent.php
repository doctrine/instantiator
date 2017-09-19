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

namespace DoctrineTest\InstantiatorPerformance;

use Doctrine\Instantiator\Instantiator;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * Performance tests for {@see \Doctrine\Instantiator\Instantiator}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @BeforeMethods({"init"})
 */
class InstantiatorPerformanceEvent
{
    /**
     * @var \Doctrine\Instantiator\Instantiator
     */
    private $instantiator;

    public function init() : void
    {
        $this->instantiator = new Instantiator();

        $this->instantiator->instantiate(__CLASS__);
        $this->instantiator->instantiate('ArrayObject');
        $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\SimpleSerializableAsset');
        $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset');
        $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\UnCloneableAsset');
    }

    /**
     * @Revs(20000)
     */
    public function benchInstantiateSelf() : void
    {
        $this->instantiator->instantiate(__CLASS__);
    }

    /**
     * @Revs(20000)
     */
    public function benchInstantiateInternalClass() : void
    {
        $this->instantiator->instantiate('ArrayObject');
    }

    /**
     * @Revs(20000)
     */
    public function benchInstantiateSimpleSerializableAssetClass() : void
    {
        $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\SimpleSerializableAsset');
    }

    /**
     * @Revs(20000)
     */
    public function benchInstantiateSerializableArrayObjectAsset() : void
    {
        $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset');
    }

    /**
     * @Revs(20000)
     */
    public function benchInstantiateUnCloneableAsset() : void
    {
        $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\UnCloneableAsset');
    }
}
