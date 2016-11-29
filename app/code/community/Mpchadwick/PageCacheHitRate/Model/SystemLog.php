<?php

class Mpchadwick_PageCacheHitRate_Model_SystemLog
{
    public function log($message, $level = null)
    {
        Mage::log($message, $level, $this->logFile(), true);
    }

    protected function logFile()
    {
        return 'mpchadwick_pagecachehitrate_system_' . date('Y_m_d') . '.log';
    }
}
