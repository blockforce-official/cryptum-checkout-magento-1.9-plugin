<?php

/**
 * Created by BlockForce Cryptum Checkout.
 * This is a sample Cryptum Checkout v1.0 API PHP client
 */

include_once('httpful.phar');
include_once('components/FormattingUtil.php');
include_once('data/ApiError.php');
include_once('data/OrderStatusEnum.php');
include_once('data/OrderCallback.php');
include_once('messages/CreateOrderRequest.php');
include_once('messages/CreateOrderResponse.php');

class CCMerchantClient
{
	private $merchantApiUrl;
	private $merchantId;

	private $apiId;
	private $debug;

	/**
	 * @param $merchantApiUrl
	 * @param $merchantId
	 * @param $apiId
	 * @param bool $debug
	 */
	function __construct($merchantApiUrl, $merchantId, $apiId, $debug = false)
	{
		$this->merchantApiUrl = $merchantApiUrl;
		$this->merchantId = $merchantId;
		$this->apiId = $apiId;
		$this->debug = $debug;
	}

	/**
	 * @param CreateOrderRequest $request
	 * @return ApiError|CreateOrderResponse
	 */

	public function createOrder(CreateOrderRequest $request)
	{
		$payload = array(
			'storeId' => $this->merchantId,
			'ecommerceOrderId' => $request->getEcommerceOrderId(),
			'ecommerce' => $request->getEcommerce(),
			'orderCurrency' => $request->getOrderCurrency(),
			'orderTotal' => $request->getOrderTotal(),
			'storeMarkupPercentage' => $request->getStoreMarkupPercentage(),
			'storeDiscountPercentage' => $request->getStoreDiscountPercentage(),
			'callbackUrl' => $request->getCallbackUrl(),
			'successReturnUrl' => $request->getSuccessReturnUrl(),
			'firstName' => $request->getFirstName(),
			'lastName' => $request->getLastName(),
			'email' => $request->getEmail(),
			'city' => $request->getCity(),
			'country' => $request->getCountry(),
			'zip' => $request->getZip(),
			'address' => $request->getAddress(),
			'complement' => $request->getComplement(),
			'state' => $request->getState()
		);

		if (!$this->debug) {

			$response = \Httpful\Request::post($this->merchantApiUrl . '/order', $payload, \Httpful\Mime::FORM)
				->body($payload, \Httpful\Mime::FORM)
				->addHeader('x-api-key', $this->apiId)
				->addHeader('Content-Type', 'application/json; charset=utf-8')
				->expects(\Httpful\Mime::JSON)
				->send();

			if ($response != null) {
				$body = $response->body;
				if ($body != null) {
					if (!isset($response->body->sessionToken)) {
						return new ApiError($response->code, "ERROR");
					} else if (isset($body->id)) {
						return new CreateOrderResponse(
							$body->id,
							$body->createdAt,
							$body->updatedAt,
							$body->orderTotal,
							$body->orderCurrency,
							$body->storeMarkupPercentage,
							$body->storeDiscountPercentage,
							$body->pluginCheckoutStoreId,
							$body->paymentStatus,
							$body->marketRates,
							$body->pluginCheckoutEcommerceId,
							$body->pluginCheckoutConsumerId,
							$body->sessionToken,
							$request->getCancelReturnUrl(),
							$request->getSuccessReturnUrl(),
							$request->getCallbackUrl(),
							$request->getEcommerceOrderId()
						);
					}
				}
			}
		} else {
			$response = \Httpful\Request::post($this->merchantApiUrl . '/order', $payload, \Httpful\Mime::FORM)
				->send();
			exit('<pre>' . print_r($response, true) . '</pre>');
		}
	}

	/**
	 * @param $r $_REQUEST
	 * @return OrderCallback|null
	 */
	public function parseCreateOrderCallback($r)
	{
		$result = null;

		if ($r != null && isset($r['order'])) {
			$result = new OrderCallback(
				$r['merchantId'],
				$r['apiId'],
				$r['orderId'],
				$r['payCurrency'],
				$r['payAmount'],
				$r['receiveCurrency'],
				$r['receiveAmount'],
				$r['receivedAmount'],
				$r['description'],
				$r['orderRequestId'],
				$r['status']
			);
		}

		return $result;
	}

	/**
	 * @param OrderCallback $c
	 * @return bool
	 */
	public function validateCreateOrderCallback(OrderCallback $c)
	{
		return true;
	}
}
