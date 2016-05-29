<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_NewRelic
    extends Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
    implements Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    public function track(array $args)
    {
        if (!function_exists('newrelic_record_custom_event')) {
            Mage::logException(new Exception('You are using the New Relic tracker, but don\'t have the agent set up properly'));
            return;
        }

        newrelic_record_custom_event('RequestResponse', $args);
    }
}
