<?php

class Mpchadwick_PageCacheHitRate_Model_TrackerFactory
{
    public function build($class)
    {
        if (class_exists($class)) {
            return new $class;
        } else {
            // We want to know about this, but an exception is better than a fatal
            // error
            Mage::throwException('Class ' . $class . ' does not exist');
        }
    }
}
