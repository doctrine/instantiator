<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;

/**
 * @template TValue
 * @template-extends ArrayObject<int, TValue>
 */
final class SerializableFinalInternalChildAsset extends ArrayObject
{
}
