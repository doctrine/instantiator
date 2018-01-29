<?php

namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;
use function trigger_error;

/**
 * A simple asset for an abstract class
 */
class WakeUpNoticesAsset extends ArrayObject
{
    /**
     * Wakeup method called after un-serialization
     */
    public function __wakeup()
    {
        trigger_error('Something went bananas while un-serializing this instance');
    }
}
