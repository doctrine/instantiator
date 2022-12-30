<?php

declare(strict_types=1);

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;

use function trigger_error;

/**
 * A simple asset for an abstract class
 *
 * @template TValue
 * @template-extends ArrayObject<int, TValue>
 */
class WakeUpNoticesAsset extends ArrayObject
{
    /**
     * Wakeup method called after un-serialization
     */
    public function __wakeup(): void
    {
        trigger_error('Something went bananas while un-serializing this instance');
    }
}
