<?php

class Mpchadwick_PageCacheHitRate_Model_Config
{
    const XML_PATH_ROOT = 'global/full_page_cache/mpchadwick_pagecachehitrate';

    public function get($path)
    {
        return Mage::getConfig()->getNode(self::XML_PATH_ROOT . '/' . $path);
    }
}
