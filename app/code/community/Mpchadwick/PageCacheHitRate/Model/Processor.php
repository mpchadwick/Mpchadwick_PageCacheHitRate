<?php

class Mpchadwick_PageCacheHitRate_Model_Processor extends Enterprise_PageCache_Model_Processor
{
    /**
     * Get page content from cache storage.
     *
     * We can't hook into any events (only one is fired by observers won't actually
     * be executed yet) if the response comes from cache, so customizing the processor
     * is out best option.
     *
     * @param string $content
     * @return string|false
     */
    public function extractContent($content)
    {
        $content = parent::extractContent($content);
        if ($content) {
            // Note: We can't resolve models through Mage::getModel() this early
            // in the execution
            $factory = new Mpchadwick_PageCacheHitRate_Model_TrackerFactory;
            $tracker = $factory->getTracker();
            if ($tracker) {
                $paramProvider = new Mpchadwick_PageCacheHitRate_Model_Tracker_ParamProvider;
                $params = $paramProvider->baseParams() + array(
                    'type' => 'hit',
                    'route' => $this->trackerRoute(),
                );
                $tracker->track($params);
            }
        }

        return $content;
    }

    /**
     * Get the route for tracking.
     *
     * This info hasn't been set on the request yet, but fortunately Magento
     * stores it as "metadata".
     *
     * @return string
     */
    protected function trackerRoute()
    {
        return $this->getMetadata('routing_requested_route') . '/' .
            $this->getMetadata('routing_requested_controller') . '/' .
            $this->getMetadata('routing_requested_action');
    }
}
