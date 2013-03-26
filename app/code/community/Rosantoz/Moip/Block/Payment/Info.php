<?php

class Rosantoz_Moip_Block_Payment_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setChild('rosantoz_moip_repay', new Rosantoz_Moip_Block_Payment_Child());

        //var_dump($this->getInfo());
    }

}