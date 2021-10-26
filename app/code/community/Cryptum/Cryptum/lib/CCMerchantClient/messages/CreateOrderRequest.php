<?php

class CreateOrderRequest
{
	private $storeId;
	private $ecommerceOrderId;
	private $ecommerce = 'wordpress';
	private $orderTotal;
	private $orderCurrency;
	private $storeMarkupPercentage;
	private $storeDiscountPercentage;
	private $cancelReturnUrl;
	private $successReturnUrl;
	private $callbackUrl;
	private $firstName;
	private $lastName;
	private $email;
	private $city;
	private $country;
	private $zip;
	private $address;
	private $complement;
	private $state;

	/**
	 * @param $storeId;
	 * @param $ecommerceOrderId;
	 * @param $ecommerce;
	 * @param $orderTotal;
	 * @param $orderCurrency;
	 * @param $storeMarkupPercentage;
	 * @param $storeDiscountPercentage;
	 * @param $cancelReturnUrl;
	 * @param $successReturnUrl;
	 * @param $callbackUrl;
	 * @param $firstName;
	 * @param $lastName;
	 * @param $email;
	 * @param $city;
	 * @param $country;
	 * @param $zip;
	 * @param $address;
	 * @param $complement;
	 * @param $state;
	 */

	function __construct($storeId, $ecommerceOrderId, $orderTotal, $orderCurrency, $storeMarkupPercentage, $storeDiscountPercentage, $cancelReturnUrl, $successReturnUrl, $callbackUrl, $firstName = "", $lastName = "", $email = "", $city = "", $country = "", $zip = "", $address = "", $complement = "", $state = "")
	{
		$this->storeId = $storeId;
		$this->ecommerceOrderId = $ecommerceOrderId;
		$this->orderTotal = $orderTotal;
		$this->orderCurrency = $orderCurrency;
		$this->storeMarkupPercentage = $storeMarkupPercentage;
		$this->storeDiscountPercentage = $storeDiscountPercentage;
		$this->cancelReturnUrl = $cancelReturnUrl;
		$this->successReturnUrl = $successReturnUrl;
		$this->callbackUrl = $callbackUrl;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->city = $city;
		$this->country = $city;
		$this->zip = $zip;
		$this->address = $address;
		$this->complement = $complement;
		$this->state = $state;
	}

		
		public function getStoreId(){
			return $this->storeId;
		}


		public function getEcommerceOrderId(){
			return $this->ecommerceOrderId;
		}


		public function getEcommerce(){
			return $this->ecommerce;
		}


		public function getOrderTotal(){
			return $this->orderTotal;
		}


		public function getOrderCurrency(){
			return $this->orderCurrency;
		}

		public function getStoreMarkupPercentage(){
			return $this->storeMarkupPercentage;
		}


		public function getStoreDiscountPercentage(){
			return $this->storeDiscountPercentage;
		}


		public function getCancelReturnUrl(){
			return $this->cancelReturnUrl;
		}


		public function getSuccessReturnUrl(){
			return $this->successReturnUrl;
		}

		public function getCallbackUrl(){
			return $this->callbackUrl;
		}


		public function getFirstName(){
			return $this->firstName;
		}


		public function getLastName(){
			return $this->lastName;
		}


		public function getEmail(){
			return $this->email;
		}

	
		public function getCity(){
			return $this->city;
		}

		
		public function getCountry(){
			return $this->country;
		}


		public function getZip(){
			return $this->zip;
		}

		public function getAddress(){
			return $this->address;
		}

		public function getComplement(){
			return $this->complement;
		}

		public function getState(){
			return $this->state;
		}



}