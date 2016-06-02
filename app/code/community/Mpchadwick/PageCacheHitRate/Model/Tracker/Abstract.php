<?php

abstract class Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
{
    public function trackContainerMisses($params)
    {
        $containers = Mage::registry('cached_page_containers');

        foreach ($containers as $container) {
            $this->track('ContainerMiss', $params + array(
                'container' => get_class($container),
            ));
        }
    }
}
