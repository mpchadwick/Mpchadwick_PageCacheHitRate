<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_ParamProvider
{
    // Taken from Cm_RedisSession_Model_Session
    const BOT_REGEX = '/^alexa|^blitz\.io|bot|^browsermob|crawl|^curl|^facebookexternalhit|feed|google web preview|^ia_archiver|^java|jakarta|^load impact|^magespeedtest|monitor|nagios|^pinterest|postrank|slurp|spider|uptime|yandex/i';

    protected $userAgent;

    protected $isBot;

    public function __construct()
    {
        $this->userAgent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        if ($this->userAgent) {
            $this->isBot = preg_match(self::BOT_REGEX, $this->userAgent);
        } else {
            // Can't be sure, but let's say it's not a bot...
            $this->isBot = 0;
        }

    }

    public function baseParams($originalRequest = false)
    {
        return array(
            'url' => $this->getUrl($originalRequest),
            'ip' => Mage::app()->getRequest()->getClientIp(),
            'hostname' => gethostname(),
            'customerGroup' => $this->getCustomerGroup(),
            'userAgent' => $this->userAgent,
            'isBot' => $this->isBot,
        );
    }

    protected function getUrl($originalRequest)
    {
        $request = Mage::app()->getRequest();

        // getRequestUri() will return the target (rewritten) URL for misses.
        // In that case, let's get the original request.
        if ($originalRequest) {
            $request = $request->getOriginalRequest();
        }

        return $request->getScheme() . '://' .
            $request->getHttpHost() .
            $request->getRequestUri();
    }

    protected function getCustomerGroup()
    {
        $value = $_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_GROUP];
        return (string)$value;
    }
}
