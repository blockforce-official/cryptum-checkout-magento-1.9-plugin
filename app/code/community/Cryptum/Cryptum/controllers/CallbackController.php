<?php

class Cryptum_Cryptum_CallbackController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var SCMerchantClient
     */
    private $client;
    /**
     * @var OrderCallback/null
     */
    private $callback;

    public function _construct()
    {
        require_once Mage::getModuleDir('', 'Cryptum_Cryptum') . DS . 'lib' . DS . 'CCMerchantClient' . DS . 'CCMerchantClient.php';
        $merchantId = Mage::getStoreConfig('payment/Cryptum/merchant_id');
        $appId = Mage::getStoreConfig('payment/Cryptum/app_id');
        $environment = Mage::getStoreConfig('payment/Cryptum_Checkout/environment');
        $merchantApiUrl = ($environment == '0') ?  "https://api-dev.cryptum.io/checkout" : "https://api.cryptum.io/checkout";
        $this->client = new CCMerchantClient($merchantApiUrl, $merchantId, $appId);
        $this->callback = $this->client->parseCreateOrderCallback($_REQUEST);
    }

    // Route: cryptum/callback/callback
    public function callbackAction()
    {
        if (!$this->getRequest()->isPost()) {
            exit;
        }

        if ($this->client->validateCreateOrderCallback($this->callback)) {
            if (Mage::getStoreConfig('payment/Cryptum/receive_currency') != $this->callback->getReceiveCurrency()) {
                echo 'Receive currency does not match.';
                exit;
            }
            $orderId = $this->callback->getOrderId();
            $order = Mage::getModel('sales/order')->load($orderId);
            switch ($this->callback->getStatus()) {
                case OrderStatusEnum::$Test:
                    // Testing
                    break;
                case OrderStatusEnum::$New:
                    $order->setData('state', Mage_Sales_Model_Order::STATE_NEW);
                    $order->save();
                    break;
                case OrderStatusEnum::$Pending:
                    $order->setData('state', Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                    $order->save();
                    break;
                case OrderStatusEnum::$Expired:
                    $order->registerCancellation("Order expired")->save();
                    $order->save();
                    break;
                case OrderStatusEnum::$Failed:
                    $order->registerCancellation("Order failed")->save();
                    break;
                case OrderStatusEnum::$Paid:
                    $this->confirmOrder($order);
                    break;
                default:
                    echo 'Unknown order status: ' . $this->callback->getStatus();
                    exit;
            }
        }
        echo '*ok*';
    }

    // Route: cryptum/callback/success
    public function successAction()
    {
        if (!isset($_GET['order'])) {
            $this->_redirectUrl(Mage::getBaseUrl());
        }
        $orderId = (int) $_GET['order'];
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order->isPaymentReview()) {
            $this->confirmOrder($order);
            $this->_redirect('sales/order/view?order_id=' . $_GET['order'], array('_secure' => true));
        }
    }

    // Route: cryptum/callback/cancel
    public function cancelAction()
    {
        if (!isset($_GET['order'])) {
            $this->_redirectUrl(Mage::getBaseUrl());
        }
        $orderId = (int) $_GET['order'];
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order->isPaymentReview() || $order->hasInvoices()) {
            $msg = "Your order could not be cancelled. Please contact customer support concerning Order ID $orderId.";
        } else {
            $msg = "Your order has been cancelled.";
            $order->registerCancellation("Order was cancelled during checkout.")->save();
        }

        Mage::getSingleton('core/session')->addError($msg);
        $this->_redirectUrl(Mage::getBaseUrl());
    }

    private function confirmOrder($order)
    {
        $payment = $order->getPayment();
        $payment->setTransactionId($order->getId())
            ->setPreparedMessage("Paid with Cryptum order {$order->getId()}.")
            ->setShouldCloseParentTransaction(true)
            ->setIsTransactionClosed(0);
        $payment->registerCaptureNotification($order->getGrandTotal());
        $order->save();
    }
}
