<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_File
    extends Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
    implements Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    protected $file = 'mpchadwick_pagecachehitrate_tracker_';

    public function track(array $args)
    {
        Mage::log(json_encode($args), null, $this->file(), true);
    }

    protected function file()
    {
        return $this->file . date('Ymd') . '.log';
    }
}
