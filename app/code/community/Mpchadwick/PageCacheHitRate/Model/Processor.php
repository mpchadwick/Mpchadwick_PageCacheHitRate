<?php

class Mpchadwick_PageCacheHitRate_Model_Processor extends Enterprise_PageCache_Model_Processor
{
    const XML_PATH_METADATA_SOURCE = 'global/full_page_cache/mpchadwick_pagecachehitrate/metadata_source';

    /**
     * Get page content from cache storage.
     *
     * We can't hook into any events (only one is fired, but observers won't actually
     * be executed yet) if the response comes from cache, so running the content
     * through a custom processor is our best option.
     *
     * @param string $content
     * @return string|false
     */
    public function extractContent($content)
    {
        if (!$content) {
            // Bail, this is a miss
            return $content;
        }

        // We can't resolve models through Mage::getModel() this early
        // in the execution
        $config = new Mpchadwick_PageCacheHitRate_Model_Config;
        $trackers = $config->get('trackers');

        if (!$trackers) {
            // Bail, we're not tracking anything
            return $content;
        }

        $paramProvider = new Mpchadwick_PageCacheHitRate_Model_Tracker_ParamProvider;
        $params = $paramProvider->baseParams() + array(
            'type' => 'hit',
            'route' => $this->trackerRoute(),
        );

        $factory = new Mpchadwick_PageCacheHitRate_Model_TrackerFactory;
        foreach ($trackers->asArray() as $alias => $data) {
            $tracker = $factory->build($data['class']);
            $tracker->track('RequestResponse', $params, $alias);
        }

        return $content;
    }

    /**
     * Get the route for tracking.
     *
     * This info hasn't been set on the request yet, but fortunately Magento
     * stores it as "metadata" in cache.
     *
     * Mpchadwick_PageCacheHitRate_Model_Processor extends Enterprise_PageCache_Model_Processor
     * but it is possible that we cannot retrieve metadata from that class. Notably, if we're
     * using Elastera_EnterprisePageCache, the documentation for which instructs us to change the
     * ee request processor.
     *
     * As such, we can configure an alternate source for metadata if needed
     *
     * @return string
     */
    protected function trackerRoute()
    {
        $configured = (string)Mage::getConfig()->getNode(self::XML_PATH_METADATA_SOURCE);

        if (class_exists($configured)) {
            $source = new $configured;
        } else {
            $source = $this;
        }

        return $source->getMetadata('routing_requested_route') . '/' .
            $source->getMetadata('routing_requested_controller') . '/' .
            $source->getMetadata('routing_requested_action');
    }
}
