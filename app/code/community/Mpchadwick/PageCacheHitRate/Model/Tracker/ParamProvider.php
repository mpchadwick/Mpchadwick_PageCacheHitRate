<?php

class Mpchadwick_PageCacheHitRate_Model_Tracker_ParamProvider
{
    public function baseParams()
    {
        return array(
            'url' => $this->getUrl(),
            'ip' => Mage::app()->getRequest()->getClientIp(),
        );
    }

    protected function getUrl()
    {
        $request = Mage::app()->getRequest();
        return $request->getScheme() . '://' .
            $request->getHttpHost() .
            $request->getRequestUri();
    }
}
