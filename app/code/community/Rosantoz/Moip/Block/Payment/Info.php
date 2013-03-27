<?php

/**
 * Info block
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
 * Info block to add a child block to Mage_Payment_Block_Info
 *
 * @category Pet_Projects
 * @package  Rosantoz_Moip
 * @author   Rodrigo dos Santos <falecom@rodrigodossantos.ws>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: <1.0>
 * @link     https://rodrigodossantos.ws
 */

class Rosantoz_Moip_Block_Payment_Info extends Mage_Payment_Block_Info
{
    /**
     * Creates a child block
     * 
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setChild(
            'rosantoz_moip_repay', 
            new Rosantoz_Moip_Block_Payment_Child()
        );
    }

}