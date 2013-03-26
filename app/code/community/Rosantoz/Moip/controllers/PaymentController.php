<?php

/**
 * Payment Controller
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
 * Payment Controller
 *
 * @category Pet_Projects
 * @package  Rosantoz_Moip
 * @author   Rodrigo dos Santos <falecom@rodrigodossantos.ws>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: <1.0>
 * @link     https://rodrigodossantos.ws
 */
class Rosantoz_Moip_PaymentController extends Mage_Core_Controller_Front_Action
{

    /**
     * Redirects users to Moip's payment page
     *
     * @return void
     */
    public function redirectAction()
    {
        $orderId = $request = $this->getRequest()->getParam('orderId');
        if (empty($orderId) || is_null($orderId)) {
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        }

        $frontEndName  = Mage::app()->getStore()->getFrontendName();
        $helper        = Mage::helper('rosantoz_moip/Moip');
        $moipAccount   = Mage::getStoreConfig('payment/rosantoz_moip/account');
        $order         = Mage::getModel('sales/order')->load($orderId);
        $total         = $helper->formatNumber($order->getData('grand_total'));
        $description   = $order->getData('increment_id');
        $transactionId = $orderId . "_" . rand();

        // Billing Data
        $billingAddressId = $order->getData('billing_address_id');
        $billing          = Mage::getModel('sales/order_address')
            ->load($billingAddressId);
        $fullName         = $billing->getData('firstname');
        $fullName .= " " . $billing->getData('lastname');
        $email     = $billing->getData('email');
        $telephone = $helper->numbersOnly($billing->getData('telephone'));
        $street    = $billing->getData('street');
        $city      = $billing->getData('city');
        $postCode  = $billing->getData('postcode');

        $blockHtml = $this->getLayout()
            ->createBlock('rosantoz_moip/redirect')
            ->setData('frontEndName', $frontEndName)
            ->setData('moipAccount', $moipAccount)
            ->setData('total', $total)
            ->setData('fullName', $fullName)
            ->setData('description', $description)
            ->setData('transactionId', $transactionId)
            ->setData('email', $email)
            ->setData('telephone', $telephone)
            ->setData('street', $street)
            ->setData('city', $city)
            ->setData('postcode', $postCode)
            ->setTemplate('rosantoz_moip/redirect.phtml')
            ->toHtml();

        $this->getResponse()->setBody($blockHtml);

    }

    /**
     * Receives POST from Moip and processes the order
     *
     * @return void
     */
    public function callBackAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $helper = Mage::helper('rosantoz_moip/Moip');
            $moip   = Mage::getModel('rosantoz_moip/Moip');

            $receivedData = $request->getPost();

            // logging received data
            Mage::log($receivedData, Zend_Log::INFO, 'rosantoz_moip.log', true);

            $paymentStatus = $receivedData['status_pagamento'];
            $paymentType   = $receivedData['tipo_pagamento'];
            $orderId       = $helper->getRealOrderId($receivedData['id_transacao']);
            $transactionId = $receivedData['cod_moip'];
            $amount        = $receivedData['valor'];

            if (is_null($transactionId) || empty($transactionId)) {
                return false;
            }

            // verifying if order exists
            $order = Mage::getModel('sales/order')->load($orderId);

            if ($order->getData()) {

                $orderAmount = $helper->formatNumber($order->getData('grand_total'));

                if ($amount != $orderAmount) {
                    $logMessage = 'Valor recebido: ' . $amount;
                    $logMessage .= ' não é igual ao valor do pedido: ';
                    $logMessage .= $helper->formatNumber(
                        $order->getData('grand_total')
                    );
                    Mage::log(
                        $logMessage,
                        Zend_Log::ERR,
                        'rosantoz_moip.log',
                        true
                    );

                    return false;
                }

                $moip->updateOrder(
                    $order,
                    $paymentStatus,
                    $paymentType,
                    $transactionId
                );

            }

            // this action only accepts post request.
            // For get requests we redirect the user to a not found page.
        } else {
            $this->_forward('defaultNoRoute');
        }
    }
}