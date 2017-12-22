<?php
namespace DoctrineTest\InstantiatorTestAsset;

use ArrayObject;

/**
 * A simple asset for an abstract class
 *
 * @author Marco Pivetta <ocramius@gmail.com>
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
