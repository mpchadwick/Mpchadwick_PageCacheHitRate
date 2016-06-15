<?php

class Mpchadwick_PageCacheHitRate_Model_TrackerFactory
{
    // We are resolving in enterprise.xml because module config hasn't been loaded
    // yet if the request is being handled through page cache
    const XML_PATH_TRACKER = 'global/full_page_cache/mpchadwick_pagecachehitrate/tracker';

    public function getTracker()
    {
        $class = (string)Mage::getConfig()->getNode(self::XML_PATH_TRACKER);
        if (!$class) {
            return false;
        }

        if (class_exists($class)) {
            return new $class;
        } else {
            // We want to know about this, but an exception is better than a fatal
            // error
            Mage::throwException('Class ' . $class . ' does not exist');
        }
    }
}
