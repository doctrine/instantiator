<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorPerformance;

use ArrayObject;
use Doctrine\Instantiator\Instantiator;
use DoctrineTest\InstantiatorTestAsset\SerializableArrayObjectAsset;
use DoctrineTest\InstantiatorTestAsset\SimpleSerializableAsset;
use DoctrineTest\InstantiatorTestAsset\UnCloneableAsset;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * Performance tests for {@see \Doctrine\Instantiator\Instantiator}
 *
 * @BeforeMethods({"init"})
 */
class InstantiatorPerformanceBench
{
    private Instantiator $instantiator;

    public function init(): void
    {
        $this->instantiator = new Instantiator();

        $this->instantiator->instantiate(self::class);
        $this->instantiator->instantiate(ArrayObject::class);
        $this->instantiator->instantiate(SimpleSerializableAsset::class);
        $this->instantiator->instantiate(SerializableArrayObjectAsset::class);
        $this->instantiator->instantiate(UnCloneableAsset::class);
    }

    /** @Revs(20000) */
    public function benchInstantiateSelf(): void
    {
        $this->instantiator->instantiate(self::class);
    }

    /** @Revs(20000) */
    public function benchInstantiateInternalClass(): void
    {
        $this->instantiator->instantiate(ArrayObject::class);
    }

    /** @Revs(20000) */
    public function benchInstantiateSimpleSerializableAssetClass(): void
    {
        $this->instantiator->instantiate(SimpleSerializableAsset::class);
    }

    /** @Revs(20000) */
    public function benchInstantiateSerializableArrayObjectAsset(): void
    {
        $this->instantiator->instantiate(SerializableArrayObjectAsset::class);
    }

    /** @Revs(20000) */
    public function benchInstantiateUnCloneableAsset(): void
    {
        $this->instantiator->instantiate(UnCloneableAsset::class);
    }
}
