<?php

class CreateOrderResponse
{

	private $id;
	private $createdAt;
	private $updatedAt;
	private $orderTotal;
	private $orderCurrency;
	private $storeMarkupPercentage;
	private $storeDiscountPercentage;
	private $pluginCheckoutStoreId;
	private $paymentStatus;
	private $marketRates;
	private $pluginCheckoutEcommerceId;
	private $pluginCheckoutConsumerId;
	private $sessionToken;

	private $cancelUrl;
	private $successUrl;
	private $callbackUrl;
	private $orderID;

	private $redirectUrl;

	/**
	 * @param $id
	 * @param $createdAt
	 * @param $updatedAt
	 * @param $orderTotal
	 * @param $orderCurrency
	 * @param $storeMarkupPercentage
	 * @param $storeDiscountPercentage
	 * @param $pluginCheckoutStoreId
	 * @param $paymentStatus
	 * @param $marketRates
	 * @param $pluginCheckoutEcommerceId
	 * @param $pluginCheckoutConsumerId
	 * @param $sessionToken
	 * 
	 * @param $cancelUrl;
	 * @param $successUrl;
	 * @param $callbackUrl;
	 * @param $orderID
	 */
	function __construct($id, $createdAt, $updatedAt, $orderTotal, $orderCurrency, $storeMarkupPercentage, $storeDiscountPercentage, $pluginCheckoutStoreId, $paymentStatus, $marketRates, $pluginCheckoutEcommerceId, $pluginCheckoutConsumerId, $sessionToken, $cancelUrl, $successUrl, $callbackUrl, $orderID)
	{
		$this->id = $id; 
		$this->createdAt = $createdAt; 
		$this->updatedAt = $updatedAt; 
		$this->orderTotal = $orderTotal; 
		$this->orderCurrency = $orderCurrency; 
		$this->storeMarkupPercentage = $storeMarkupPercentage; 
		$this->storeDiscountPercentage = $storeDiscountPercentage; 
		$this->pluginCheckoutStoreId = $pluginCheckoutStoreId; 
		$this->paymentStatus = $paymentStatus; 
		$this->marketRates = $marketRates; 
		$this->pluginCheckoutEcommerceId = $pluginCheckoutEcommerceId; 
		$this->pluginCheckoutConsumerId = $pluginCheckoutConsumerId; 
		$this->sessionToken = $sessionToken;

		$this->cancelUrl = $cancelUrl;
		$this->successUrl = $successUrl;
		$this->callbackUrl = $callbackUrl;	
		$this->orderID = $orderID;

		Mage::getConfig()->saveConfig('payment/Cryptum_Checkout/orderId', $id);
		$environment = Mage::getStoreConfig('payment/Cryptum_Checkout/environment');	
		$this->redirectUrl = ($environment == '0') ? "https://plugin-checkout-dev.cryptum.io/public/payment-details.html" : "https://plugin-checkout.cryptum.io/public/payment-details.html";
	}


	public function getId()
	{
		return $this->id;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	public function getOrderTotal()
	{
		return $this->orderTotal;
	}

	public function getOrderCurrency()
	{
		return $this->orderCurrency;
	}

	public function getStoreMarkupPercentage()
	{
		return $this->storeMarkupPercentage;
	}

	public function getStoreDiscountPercentage()
	{
		return $this->storeDiscountPercentage;
	}

	public function getPluginCheckoutStoreId()
	{
		return $this->pluginCheckoutStoreId;
	}

	public function getPaymentStatus()
	{
		return $this->paymentStatus;
	}

	public function getMarketRates()
	{
		return $this->marketRates;
	}

	public function getPluginCheckoutEcommerceId()
	{
		return $this->pluginCheckoutEcommerceId;
	}

	public function getPluginCheckoutConsumerId()
	{
		return $this->pluginCheckoutConsumerId;
	}

	public function getSessionToken()
	{
		return $this->sessionToken;
	}

	public function getRedirectUrl()
	{
		$query_params = [
			'cancelReturnUrl' => $this->cancelUrl,
			'successReturnUrl' =>  $this->successUrl,
			'callbackUrl' => $this->callbackUrl,
			'sessionToken' => $this->getSessionToken(),
			'orderId' => $this->getId(),
			'ecommerceOrderId' => $this->orderID,
			'environment' => 'test'
		];

		$form_params_joins = '';
		foreach ($query_params as $key => $value) {
			$form_params_joins .= $key . '=' . $value . '&';
		}

		$_redirect = $this->redirectUrl . '?' . $form_params_joins;

		return stripslashes($_redirect);
	}
}