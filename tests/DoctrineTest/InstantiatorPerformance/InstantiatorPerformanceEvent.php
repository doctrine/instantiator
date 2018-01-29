<?php

namespace DoctrineTest\InstantiatorPerformance;

use Doctrine\Instantiator\Instantiator;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * Performance tests for {@see \Doctrine\Instantiator\Instantiator}
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
