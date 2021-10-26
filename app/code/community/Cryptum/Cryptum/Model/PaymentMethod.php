<?php

class Cryptum_Cryptum_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'Cryptum';

    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;

    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;

    /**
     * Can capture funds online?
     */
    protected $_canCapture              = false;

    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;

    /**
     * Can refund online?
     */
    protected $_canRefund               = false;

    /**
     * Can void transactions online?
     */
    protected $_canVoid                 = false;

    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;

    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;

    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = true;

    /**
     * Can save credit card information for future processing?
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        require_once Mage::getModuleDir('', 'Cryptum_Cryptum') . DS . 'lib' . DS . 'CCMerchantClient' . DS . 'CCMerchantClient.php';

        $merchantId = Mage::getStoreConfig('payment/Cryptum/merchant_id');
        $environment = Mage::getStoreConfig('payment/Cryptum/environment');
        $appId = Mage::getStoreConfig('payment/Cryptum/app_id');
        $storeMarkupPercentage =  Mage::getStoreConfig('payment/Cryptum/store_markup_percentage');
        $storeDiscountPercentage =  Mage::getStoreConfig('payment/Cryptum/store_discount_percentage');

        $order = $payment->getOrder();
        $orderID = $order->getId();
        $currency = $order->getBaseCurrencyCode();

        $callbackUrl = Mage::app()->getStore()->getUrl('cryptum/callback/callback');
        $successUrl = Mage::app()->getStore()->getUrl('cryptum/callback/success?order=' . $orderID);
        $cancelUrl = Mage::app()->getStore()->getUrl('cryptum/callback/cancel?order=' . $orderID);

        $merchantApiUrl = ($environment == '0') ?  "https://api-dev.cryptum.io/checkout" : "https://api.cryptum.io/checkout";
        $client = new CCMerchantClient($merchantApiUrl, $merchantId, $appId);

        $orderRequest = new CreateOrderRequest($merchantId, $orderID, $order->getGrandTotal(), $currency, $storeMarkupPercentage, $storeDiscountPercentage, $cancelUrl, $successUrl, $callbackUrl);
        $response = $client->createOrder($orderRequest);
        
        Mage::log($response, null, "merchant.log", true);

        if ($response instanceof ApiError) {
            Mage::throwException(Mage::helper('payment')->__('Cryptum error. Error code: ' . $response->getCode() . '. Message: ' . $response->getMessage()));
        } else {
            $redirectUrl = $response->getRedirectUrl();
            $payment->setIsTransactionPending(true);
            Mage::getSingleton('customer/session')->setRedirectUrl(stripslashes($redirectUrl));
            Mage::log(Mage::getSingleton('customer/session'), null, "merchant.log", true);
        }
        return $this;
    }
}
