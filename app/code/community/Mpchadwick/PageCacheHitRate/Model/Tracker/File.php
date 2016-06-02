<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_File
    extends Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
    implements Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    protected $file = 'mpchadwick_pagecachehitrate_';

    public function track($type, array $args)
    {
        Mage::log(json_encode($args), null, $this->file($type), true);
    }

    protected function file($type)
    {
        return $this->file . $type . '_' . date('Ymd') . '.log';
    }
}
