<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_NewRelic
    extends Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
    implements Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    protected function _track($type, array $args, $alias)
    {
        if (!function_exists('newrelic_record_custom_event')) {
            $msg = 'You are using the New Relic tracker, but don\'t have the agent set up properly';
            $this->logger->log($msg);
            return;
        }

        newrelic_record_custom_event($type, $args);
    }
}
