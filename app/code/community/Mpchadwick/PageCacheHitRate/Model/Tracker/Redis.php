<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_Redis
    extends Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
    implements Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    const KEY_PREFIX = 'mpchadwick_pagecachehitrate_';

    /** @var Credis_Client */
    protected $redis;

    /** @var Mpchadwick_PageCacheHitRate_Model_Config */
    protected $config;

    public function __construct()
    {
        $this->config = new Mpchadwick_PageCacheHitRate_Model_Config;
    }

    protected function _track($type, array $args, $alias)
    {
        $result = $this->setupConnection($alias);
        if (!$result) {
            // Bail. setUpConnection handled the error already
            return;
        }

        $this->redis->hIncrBy(
            self::KEY_PREFIX . $type,
            $this->field($args),
            1
        );
    }

    protected function setupConnection($alias)
    {
        if (!is_null($this->redis)) {
            return;
        }

        $prefix = 'trackers/' . $alias . '/';
        $server = $this->config->get($prefix . 'server');
        $port = $this->config->get($prefix . 'port');
        $db = $this->config->get($prefix) . 'database';

        if (!$server || !$port || !$db) {
            $message = 'Missing parameters for creating Redis connection';
            Mage::logException(new Exception($message));
            return false;
        }

        try {
            $this->redis = new Credis_Client(
                $this->config->get($prefix . 'server'),
                $this->config->get($prefix . 'port')
            );
            $this->redis->connect();
            $this->redis->select($this->config->get($prefix . 'database'));
        } catch (Exception $e) {
            // @todo Why is Mage::logException() leading to a 404?
            return false;
        }

        return true;
    }

    protected function field($args)
    {
        ksort($args);
        return http_build_query($args);
    }
}