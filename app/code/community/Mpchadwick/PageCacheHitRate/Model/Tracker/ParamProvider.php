<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_ParamProvider
{
    public function baseParams($originalRequest = false)
    {
        return array(
            'url' => $this->getUrl($originalRequest),
            'ip' => Mage::app()->getRequest()->getClientIp(),
            'hostname' => gethostname(),
            'customerGroup' => $this->getCustomerGroup(),
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
