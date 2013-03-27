<?php

/**
 * Child block
 *
 * PHP version 5.3.8
 *
 * @category Pet_Projects
 * @package  Rosantoz_Moip
 * @author   Rodrigo dos Santos <falecom@rodrigodossantos.ws>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     https://rodrigodossantos.ws
 */

/**
 * Child block to show a payment link on user's account
 *
 * @category Pet_Projects
 * @package  Rosantoz_Moip
 * @author   Rodrigo dos Santos <falecom@rodrigodossantos.ws>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: <1.0>
 * @link     https://rodrigodossantos.ws
 */
class Rosantoz_Moip_Block_Payment_Child extends Mage_Core_Block_Template
{
    /**
     * Shows a payment link on user's account
     * 
     * @return void
     */
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