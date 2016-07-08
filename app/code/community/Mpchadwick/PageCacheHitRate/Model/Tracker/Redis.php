<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_Redis
    extends Mpchadwick_PageCacheHitRate_Model_Tracker_Abstract
    implements Mpchadwick_PageCacheHitRate_Model_TrackerInterface
{
    const KEY_PREFIX = 'mpchadwick_pagecachehitrate_';

    /** @var Credis_Client */
    protected $redis;

    public function connection($alias)
    {
        $this->setupConnection($alias);
        return $this->redis;
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
            return true;
        }

        $prefix = 'trackers/' . $alias . '/';
        $server = $this->config->get($prefix . 'server');
        $port = $this->config->get($prefix . 'port');
        $db = $this->config->get($prefix) . 'database';

        if (!$server || !$port || !$db) {
            $this->logger->log('Missing parameters for creating Redis connection', Zend_Log::ERR);
            return false;
        }

        try {
            $this->redis = new Credis_Client(
                (string)$this->config->get($prefix . 'server'),
                (int)$this->config->get($prefix . 'port')
            );
            $this->redis->connect();
            (int)$this->redis->select($this->config->get($prefix . 'database'));
        } catch (Exception $e) {
            $this->logger->log($e->getMessage(), Zend_Log::ERR);
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
