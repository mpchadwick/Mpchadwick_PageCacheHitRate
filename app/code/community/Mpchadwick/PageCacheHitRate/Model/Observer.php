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
            'cacheable' => $this->isRequestCacheable()
        );

        $factory = Mage::getModel('mpchadwick_pagecachehitrate/trackerFactory');
        foreach ($trackers->asArray() as $alias => $data) {
            $tracker = $factory->build($data['class']);
            $tracker->track('RequestResponse', $params, $alias);

            // Track any container misses for a partial cache response
            $trackContainerMisses = (string)$config->get('track_container_misses');
            if ($type === 'partial' && $trackContainerMisses) {
                $tracker->trackContainerMisses($params, $alias);
            }
        }
    }

    /**
     * Determine this request is cacheable in FPC
     *
     * @return bool
     */
    protected function isRequestCacheable()
    {
        $request = Mage::app()->getRequest();
        $processor = Mage::getSingleton('enterprise_pagecache/processor');
        $subprocessor = $processor->getMetadata('cache_subprocessor');

        return $subprocessor !== null && $processor->canProcessRequest($request);
    }

    /**
     * Get the type of response.
     *
     * Partial hits will be handled by `pagecache/request/process`.
     *
     * @return string
     */
    protected function type()
    {
        $request = Mage::app()->getRequest();

        if ($request->getModuleName() === 'pagecache' &&
            $request->getControllerName() === 'request' &&
            $request->getActionName() === 'process'
        ) {
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
