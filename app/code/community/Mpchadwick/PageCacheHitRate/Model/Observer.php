<?php

class Mpchadwick_PageCacheHitRate_Model_Observer
{
    /**
     * Handle the controller_front_send_response_before event.
     *
     * This won't happen in the case of a hit. However it will happen for both
     * a miss and a partial hit.
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
        $params = $paramProvider->baseParams(true) + array(
            'type' => $this->type(),
            'route' => $this->trackerRoute(),
        );

        // @todo Might be useful to know what containers prevented a full hit in the case of a partial
        $tracker->track($params);
    }

    /**
     * Get the type of response.
     *
     * Enterprise_PageCache_Model_Processor::_processContent() will store an array of
     * `cached_page_containers` in Mage::_registry for partial hits.
     *
     * @return string
     */
    protected function type()
    {
        if (Mage::registry('cached_page_containers')) {
            return 'partial';
        } else {
            return 'miss';
        }
    }

    /**
     * Get the route for tracking.
     *
     * Same mechanics used to store metadata for page cache, which is our only
     * option for determining the route when coming from page cache.
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
