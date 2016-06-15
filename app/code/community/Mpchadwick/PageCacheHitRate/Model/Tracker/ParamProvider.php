<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_ParamProvider
{
    public function baseParams($originalRequest = false)
    {
        return array(
            'url' => $this->getUrl($originalRequest),
            'ip' => Mage::app()->getRequest()->getClientIp(),
            'hostname' => gethostname(),
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
}
