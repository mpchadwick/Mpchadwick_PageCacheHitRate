<?php

interface Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    public function track($type, array $args, $alias);
}
