<?php

/**
 * Module model
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
 * Module model
 *
 * @category Pet_Projects
 * @package  Rosantoz_Moip
 * @author   Rodrigo dos Santos <falecom@rodrigodossantos.ws>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: <1.0>
 * @link     https://rodrigodossantos.ws
 */
class Rosantoz_MoIP_Model_MoIP extends Mage_Payment_Model_Method_Abstract
{

    protected $_code = 'rosantoz_moip';

    /**
     * Returns the URL to redirect user
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('rosantoz_moip/payment/redirect');
    }

    /**
     * Generates the invoice for the order,
     * add a comment to the order and notifies the user
     *
     * @param object $order         Mage_Sales_Model_Order object
     * @param string $paymentType   Payment type (credit card, boleto, etc.)
     * @param int    $transactionId Moip's transaction id
     *
     * @return void
     */
    public function generateInvoice($order, $paymentType, $transactionId)
    {
        $invoice = $order->prepareInvoice();
        $invoice->register()->pay();

        $comment = "Pagamento por " . $paymentType;
        $comment .= " aprovado pelo Moip. Transação #" . $transactionId;
        $invoice->addComment($comment, true, true)->save();

        $invoice->sendUpdateEmail(true, $comment);
        $invoice->setEmailSent(true);

        Mage::getModel('core/resource_transaction')->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        $invoiceId = $invoice->getIncrementId();
        $comment   = "Fatura " . $invoiceId . " criada.";
        $newStatus = Mage::getStoreConfig(
            'payment/rosantoz_moip/approved_payment_order_status'
        );
        $order->setState($newStatus, $newStatus, $comment, true)->save();

    }

    /**
     * Updates the order status and write history comments
     *
     * @param object $order         Mage_Sales_Model_Order object
     * @param int    $paymentStatus Payment status number
     * @param string $paymentType   Payment type (credit card, boleto, etc.)
     * @param int    $transactionId Moip's transaction id
     *
     * @return void
     */
    public function updateOrder($order, $paymentStatus, $paymentType, $transactionId)
    {
        $helper = Mage::helper('rosantoz_moip/Moip');
        switch ($paymentStatus) {

        case 1: // approved
            $order->addStatusHistoryComment(
                $helper->getCommentByStatus($paymentStatus)
            );
            $this->generateInvoice($order, $paymentType, $transactionId);
            break;

        case 5: // cancelled
            $comment   = $helper->getCommentByStatus($paymentStatus);
            $newStatus = Mage::getStoreConfig(
                'payment/rosantoz_moip/cancelled_payment_order_status'
            );
            $order->setState($newStatus, $newStatus, $comment, true)->save();
            break;

        default:
            $order->addStatusHistoryComment(
                $helper->getCommentByStatus($paymentStatus)
            )
                ->save();
            break;

        }

    }


}
