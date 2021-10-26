<?php


class Cryptum_Cryptum_Block_LastPayment extends Mage_Checkout_Block_Cart_Totals{

    public function needDisplayBaseGrandtotal(){
        return false;
    }

}