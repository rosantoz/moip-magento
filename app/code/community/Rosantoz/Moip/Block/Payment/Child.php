<?php

class Rosantoz_Moip_Block_Payment_Child extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $request = $this->getRequest();
        $this->setTemplate('rosantoz_moip/payment/info/default.phtml');

        $redirectUrl = $this->getUrl();
        $redirectUrl .= 'rosantoz_moip/payment/redirect/orderId/';
        $redirectUrl .= $request->getParam('order_id');

        $this->setData('redirectUrl', $redirectUrl);
    }
}