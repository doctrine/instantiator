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

namespace InstantiatorPerformance;

use Athletic\AthleticEvent;
use Instantiator\Instantiator;

/**
 * Performance tests for {@see \Instantiator\Instantiator}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class InstantiatorPerformanceEvent extends AthleticEvent
{
    /**
     * @var \Instantiator\Instantiator
     */
    private $cleanInstantiator;

    /**
     * @var \Instantiator\Instantiator
     */
    private $warmedUpInstantiator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->cleanInstantiator    = new Instantiator();
        $this->warmedUpInstantiator = new Instantiator();

        $this->warmedUpInstantiator->instantiate(__CLASS__);
        $this->warmedUpInstantiator->instantiate('ArrayObject');
        $this->warmedUpInstantiator->instantiate('InstantiatorTestAsset\\SimpleSerializableAsset');
        $this->warmedUpInstantiator->instantiate('InstantiatorTestAsset\\SerializableArrayObjectAsset');
        $this->warmedUpInstantiator->instantiate('InstantiatorTestAsset\\UnCloneableAsset');
    }

    /**
     * @iterations 20000
     * @baseline
     * @group simple-class
     */
    public function testInstantiateSelfWithoutWarmup()
    {
        $this->cleanInstantiator->instantiate(__CLASS__);
    }

    /**
     * @iterations 20000
     * @group simple-class
     */
    public function testInstantiateSelfWithWarmup()
    {
        $this->warmedUpInstantiator->instantiate(__CLASS__);
    }

    /**
     * @iterations 20000
     * @baseline
     * @group internal-class
     */
    public function testInstantiateInternalClassWithoutWarmup()
    {
        $this->cleanInstantiator->instantiate('ArrayObject');
    }

    /**
     * @iterations 20000
     * @group internal-class
     */
    public function testInstantiateInternalClassWitWarmup()
    {
        $this->warmedUpInstantiator->instantiate('ArrayObject');
    }

    /**
     * @iterations 20000
     * @baseline
     * @group serializable-class
     */
    public function testInstantiateSimpleSerializableAssetClassWithoutWarmup()
    {
        $this->cleanInstantiator->instantiate('InstantiatorTestAsset\\SimpleSerializableAsset');
    }

    /**
     * @iterations 20000
     * @group serializable-class
     */
    public function testInstantiateSimpleSerializableAssetClassWithWarmup()
    {
        $this->warmedUpInstantiator->instantiate('InstantiatorTestAsset\\SimpleSerializableAsset');
    }

    /**
     * @iterations 20000
     * @baseline
     * @group internal-serializable-class
     */
    public function testInstantiateSerializableArrayObjectAssetWithoutWarmup()
    {
        $this->cleanInstantiator->instantiate('InstantiatorTestAsset\\SerializableArrayObjectAsset');
    }

    /**
     * @iterations 20000
     * @group internal-serializable-class
     */
    public function testInstantiateSerializableArrayObjectAssetWithWarmup()
    {
        $this->warmedUpInstantiator->instantiate('InstantiatorTestAsset\\SerializableArrayObjectAsset');
    }

    /**
     * @iterations 20000
     * @baseline
     * @group internal-serializable-class
     */
    public function testInstantiateUnCloneableAssetWithoutWarmup()
    {
        $this->cleanInstantiator->instantiate('InstantiatorTestAsset\\UnCloneableAsset');
    }

    /**
     * @iterations 20000
     * @group internal-serializable-class
     */
    public function testInstantiateUnCloneableAssetWithWarmup()
    {
        $this->warmedUpInstantiator->instantiate('InstantiatorTestAsset\\UnCloneableAsset');
    }
}
