<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayIterator;

/**
 * @template TValue
 * @template-extends ArrayIterator<int, TValue>
 */
final class SerializableFinalInternalChildAsset extends ArrayIterator
{
}
