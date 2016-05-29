<?php

class Mpchadwick_PageCacheHitRate_Model_Observer
{
    /**
     * This won't happen if the page is coming from cache.
     *
     * In that case, let's track a cache miss.
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function handleControllerFrontSendResponseBefore(Varien_Event_Observer $observer)
    {
        $factory = Mage::getModel('mpchadwick_pagecachehitrate/trackerFactory');
        $tracker = $factory->getTracker();

        // A tracker isn't configured. Bail.
        if (!$tracker) {
            return;
        }

        $paramProvider = Mage::getModel('mpchadwick_pagecachehitrate/tracker_paramProvider');
        $params = $paramProvider->baseParams() + array(
            'type' => 'miss',
            'route' => $this->trackerRoute(),
        );
        $tracker->track($params);
    }

    /**
     * Get the route for tracking.
     *
     * Same mechanics used to store metadata for page cache, which is our only
     * option for determining the route when coming from page cache
     *
     * @return string
     */
    protected function trackerRoute()
    {
        return Mage::app()->getRequest()->getRequestedRouteName() . '/' .
            Mage::app()->getRequest()->getRequestedControllerName() . '/' .
            Mage::app()->getRequest()->getRequestedActionName();
    }
}
