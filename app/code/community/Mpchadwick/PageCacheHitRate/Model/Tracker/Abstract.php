<?php

abstract class Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
{
    abstract protected function _track($type, array $args, $alias);

    public function track($type, array $args, $alias)
    {
        $config = new Mpchadwick_PageCacheHitRate_Model_Config;
        $strip = $config->get('trackers/' . $alias . '/strip');
        if ($strip) {
            foreach ($strip->asArray() as $key => $val) {
                unset($args[$key]);
            }
        }

        $this->_track($type, $args, $alias);
    }

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
