<?php
class Df_Chronopay_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code  = 'chronopay_standard';
	protected $_formBlockType = 'df_chronopay/standard_form';
	protected $_isGateway			   = false;
	protected $_canAuthorize			= true;
	protected $_canCapture			  = true;
	protected $_canCapturePartial	   = false;
	protected $_canRefund			   = false;
	protected $_canVoid				 = false;
	protected $_canUseInternal		  = false;
	protected $_canUseCheckout		  = true;
	protected $_canUseForMultishipping  = false;
	protected $_order = null;

	/**
	 * @param null $quote
	 * @return bool
	 */
	public function isAvailable($quote = null) {
		return df_enabled(Df_Core_Feature::CHRONOPAY) && parent::isAvailable();
	}

	/**
	 * @param string $field
	 * @param null|int $storeId
	 * @return string
	 */
	public function getConfigData($field, $storeId = null) {
		if (null === $storeId) {
			$storeId = $this->getDataUsingMethod('store');
		}
		/** @var string $path */
		$path = rm_config_key('df_payment', $this->getCode(), $field);
		return Mage::getStoreConfig($path, $storeId);
	}

	/** @return Df_Chronopay_Model_Standard */
	public function validate() {
		parent::validate();
		$paymentInfo = $this->getInfoInstance();
		if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
			$currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
		} else {
			$currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
		}
		if ($currency_code != $this->getConfigData('currency')) {
			Mage::throwException(df_h()->chronopay()->__('Selected currency code ('.$currency_code.') is not compatabile with ChronoPay'));
		}
		return $this;
	}

	/**
	 * @param Varien_Object $payment
	 * @param float $amount
	 * @return Df_Chronopay_Model_Standard
	 */
	public function capture(Varien_Object $payment, $amount)
	{
		$payment
			->setStatus(
				self::STATUS_APPROVED
			)
			->setLastTransId(
				$this->getTransactionId()
			)
		;
		return $this;
	}

	/** @return string */
	public function getChronopayUrl()
	{
		return 'https://payments.chronopay.com/';
	}

	/** @return string */
	protected function getSuccessURL()
	{
		return Mage::getUrl('df_chronopay/standard/success', array('_secure' => false));
	}

	/** @return string */
	protected function getNotificationURL()
	{
		return Mage::getUrl('df_chronopay/standard/notify', array('_secure' => false));
	}

	/** @return string */
	protected function getFailureURL()
	{
		return Mage::getUrl('df_chronopay/standard/failure', array('_secure' => false));
	}

	/**
	 * @param string $name
	 * @return Df_Chronopay_Block_Standard_Form
	 */
	public function createFormBlock($name) {
		return
			Df_Chronopay_Block_Standard_Form::i($name)
				->setMethod($this->_code)
				->setPayment($this->getPayment())
		;
	}

	/** @return string */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('df_chronopay/standard/redirect');
	}

	/** @return Mage_Sales_Model_Order_Address */
	public function getBillingAddress() {
		return $this->getOrder()->getBillingAddress();
	}

	/** @return string */
	public function getFirstName() {
		return
			$this->formatName(
				rm_first(
					explode(
						' '
						,df_nts($this->getBillingAddress()->getFirstname())
					)
				)
				,'FIRSTNAME'
			)
		;
	}

	/** @return string */
	public function getLastName() {
		$lastName = df_trim(df_nts($this->getBillingAddress()->getLastname()));
		if (2 > mb_strlen($lastName)) {
			/** @var string[] $firstNameExploded */
			$firstNameExploded =
				explode(
					' '
					,df_nts($this->getBillingAddress()->getFirstname())
				)
			;
			if (1 < count($firstNameExploded)) {
				$lastName = rm_last($firstNameExploded);
			}
		}
		return $this->formatName(df_nts($lastName), 'LASTNAME');
	}

	/** @return array */
	public function getStandardCheckoutFormFields() {
		$order = $this->getOrder();
		if (!($order instanceof Mage_Sales_Model_Order)) {
			Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
		}
		/** @var Mage_Customer_Model_Address_Abstract $billingAddress */
		$billingAddress = $order->getBillingAddress();
		$streets = $billingAddress->getStreet();
		$street = isset($streets[0]) && $streets[0] != ''
				  ? $streets[0]
				  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');
//		if ($this->getConfigData('description')) {
//			$transDescription = $this->getConfigData('description');
//		} else {
//			$transDescription = df_h()->chronopay()->__('Order #%s', $order->getRealOrderId());
//		}

		if ($order->getCustomerEmail()) {
			$email = $order->getCustomerEmail();
		} else if ($billingAddress->getEmail()) {
			$email = $billingAddress->getEmail();
		} else {
			$email = '';
		}

		$price =
			number_format(
				(float)$order->getBaseGrandTotal()
				,2
				,'.'
				,''
			)
		;

		/** @var string $postalCode */
		$postalCode = df_trim(df_nts($billingAddress->getPostcode()));
		if (2 > mb_strlen($postalCode)) {
			$postalCode = '000000';
		}
		$fields =
			array(
				'product_id' => $this->getConfigData('product_id')
				,'product_price' => $price
				,'sign' =>
					md5(
						implode(
							'-'
							,array(
								$this->getConfigData('product_id')
								,$price
								,df_mage()->coreHelper()->decrypt(
									$this->getConfigData('shared_sec')
								)
							)
						)
					)
				,'language' => strtolower($this->getConfigData('language'))
				,'f_name'  => $this->getFirstName()
				,'s_name' => $this->getLastName()
				,'street' => $street
				,'city' => $billingAddress->getCity()
				,'zip' => $postalCode
				,'country' => $billingAddress->getCountryModel()->getIso3Code()
				,'phone' => $billingAddress->getTelephone()
				,'email' => $email
				,'cb_url' => $this->getNotificationURL()
				,'cb_type' => 'P' // POST method used (G - GET method)
				,'decline_url' => $this->getFailureURL()
				,'success_url' => $this->getSuccessURL()
				,'cs1' => df_mage()->coreHelper()->encrypt($order->getRealOrderId())
			)
		;
		/**
		 * ChronoPay разрешает указывать код региона только для США и Канады
		 * @link http://magento-forum.ru/topic/3294/
		 */

		/** @var string $countriesWithRecognizableRegions */
		$countriesWithRecognizableRegions =
			array(
				Df_Directory_Helper_Country::ISO_2_CODE__USA
				,Df_Directory_Helper_Country::ISO_2_CODE__CANADA
			)
		;
		if (
			in_array(
				$billingAddress->getCountryModel()->getIso2Code()
				,$countriesWithRecognizableRegions
			)
		)  {
			/** @var Mage_Directory_Model_Region $regionModel */
			$regionModel = $billingAddress->getRegionModel();
			df_assert($regionModel instanceof Mage_Directory_Model_Region);
			if ($regionModel->getCode()) {
				$fields['state'] = $regionModel->getCode();
			}
		}
		return $fields;
	}

	/**
	 * @throws Exception
	 * @param array $data
	 * @return Exception
	 */
	public function validateResponse(array $data) {
		$order = $this->getOrder();
		if (!($order instanceof Mage_Sales_Model_Order)) {
			Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
		}
		try {
			$ok = is_array($data)
				&& isset($data['transaction_type']) && $data['transaction_type'] != ''
				&& isset($data['customer_id']) && $data['customer_id'] != ''
				&& isset($data['site_id']) && $data['site_id'] != ''
				&& isset($data['product_id']) && $data['product_id'] != '';
			if (!$ok) {
				throw new Exception('Cannot restore order or invalid order ID');
			}

			// validate site ID
			if ($this->getConfigData('site_id') != $data['site_id']) {
				throw new Exception('Invalid site ID');
			}

			// validate product ID
			if ($this->getConfigData('product_id') != $data['product_id']) {
				throw new Exception('Invalid product ID');
			}

			// Successful transaction type
			if (!in_array($data['transaction_type'], array('initial', 'onetime', 'Purchase'))) {
				throw new Exception('Transaction is not successful');
			}

		} catch (Exception $e) {
			return $e;
		}
	}

	/**
	 * @param string $name
	 * @param string $defaultName
	 * @return string
	 */
	private function formatName($name, $defaultName) {
		df_param_string($name, 0);
		$name = df_trim($name);
		if (2 > mb_strlen($name)) {
			$name = $defaultName;
		}
		/** @var string $result */
		$result = strtoupper(df_output()->transliterate($name));
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Chronopay_Model_Standard
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}