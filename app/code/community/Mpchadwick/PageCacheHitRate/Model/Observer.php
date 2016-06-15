<?php

class Mpchadwick_PageCacheHitRate_Model_Observer
{
    const XML_PATH_TRACK_CONTAINER_MISSES = 'global/full_page_cache/mpchadwick_pagecachehitrate/track_container_misses';

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
        $config = Mage::getModel('mpchadwick_pagecachehitrate/config');
        $trackers = $config->get('trackers');
        if (!$trackers) {
            // We're not tracking, bail...
            return;
        }

        $paramProvider = Mage::getModel('mpchadwick_pagecachehitrate/tracker_paramProvider');
        $type = $this->type();
        $params = $paramProvider->baseParams(true) + array(
            'type' => $type,
            'route' => $this->trackerRoute(),
        );

        $factory = Mage::getModel('mpchadwick_pagecachehitrate/trackerFactory');
        foreach ($trackers->asArray() as $data) {
            $tracker = $factory->build($data['class']);
            $tracker->track('RequestResponse', $params);

            // Track any container misses for a partial cache response
            $trackContainerMisses = (string)Mage::getConfig()->getNode(self::XML_PATH_TRACK_CONTAINER_MISSES);
            if ($type === 'partial' && $trackContainerMisses) {
                unset($params['type']);
                $tracker->trackContainerMisses($params);
            }
        }
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
