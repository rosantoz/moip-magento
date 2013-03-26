<?php

/**
 * Module Helper
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
 * Module Helper
 *
 * @category Pet_Projects
 * @package  Rosantoz_Moip
 * @author   Rodrigo dos Santos <falecom@rodrigodossantos.ws>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: <1.0>
 * @link     https://rodrigodossantos.ws
 */
class Rosantoz_Moip_Helper_Moip extends Mage_Core_Helper_Abstract
{

    /**
     * Formats the number to fit in Moip's standard
     *
     * @param float $number number/amount
     *
     * @return mixed
     */
    public function formatNumber($number)
    {
        return str_replace(".", "", sprintf('%.2f', $number));
    }

    /**
     * Filters the input string and returns numbers only
     *
     * @param string $string Input string
     *
     * @return string
     */
    public function numbersOnly($string)
    {
        return preg_replace('([^0-9])', '', $string);
    }

    /**
     * Returns the real order id
     *
     * @param string $orderId Order id received from Moip
     *
     * @return int
     */
    public function getRealOrderId($orderId)
    {
        $exp = explode("_", $orderId);

        return $exp[0];
    }

    /**
     * Returns a comment according to the payment status
     *
     * @param int $paymentStatus Payment status received from Moip
     *
     * @return mixed
     */
    public function getCommentByStatus($paymentStatus)
    {
        $arr = array(
            '1' => 'Pagamento já foi realizado porém ainda não foi creditado na Carteira Moip recebedora (devido ao floating da forma de pagamento',
            '2' => 'Pagamento está sendo realizado ou janela do navegador foi fechada (pagamento abandonado)',
            '3' => 'Boleto foi impresso e ainda não foi pago',
            '4' => 'Pagamento já foi realizado e dinheiro já foi creditado na Carteira Moip recebedora',
            '5' => 'Pagamento foi cancelado pelo pagador, instituição de pagamento, Moip ou recebedor antes de ser concluído',
            '6' => 'Pagamento foi realizado com cartão de crédito e autorizado, porém está em análise pela Equipe Moip. Não existe garantia de que será concluído',
            '7' => 'Pagamento foi estornado pelo pagador, recebedor, instituição de pagamento ou Moip',
        );

        if (array_key_exists($paymentStatus, $arr)) {
            return $arr[$paymentStatus];
        }

        return null;
    }
}