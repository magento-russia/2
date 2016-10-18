<?php
abstract class Df_Payment_Model_Request_Payment extends Df_Payment_Model_Request {
	/**
	 * @abstract
	 * @used-by params()
	 * @return array(string => string|int)
	 */
	abstract protected function _params();

	/**
	 * @used-by Df_IPay_Action_GetPaymentAmount::_process()
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	public function city() {return $this->address()->getCity();}

	/**
	 * @used-by Df_Uniteller_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function countryName() {return $this->address()->getCountryModel()->getName();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function currencyCode() {return $this->configS()->getCurrencyCodeInServiceFormat();}

	/** @return string */
	protected function getTransactionDescription() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = str_replace(
				array_keys($this->getTransactionDescriptionParams())
				,array_values($this->getTransactionDescriptionParams())
				,$this->configS()->getTransactionDescription()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function email() {return $this->order()->getCustomerEmail();}

	/** @return string */
	public function getCustomerIpAddress() {
		return !df_controller() ? '' : df_controller()->getRequest()->getClientIp();
	}

	/** @return string */
	protected function getCustomerNameFull() {
		return df_ccc(' '
			,$this->order()->getCustomerLastname()
			,$this->order()->getCustomerFirstname()
			,$this->order()->getCustomerMiddlename()
		);
	}

	/** @return string */
	protected function urlCustomerReturn() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->method()->getCustomerReturnUrl($this->order());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getTransactionDescription
	 * @return Zend_Uri_Http
	 */
	protected function getStoreUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$this->{__METHOD__} = Zend_Uri_Http::fromString(
				$this->store()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	protected function getTransactionDescriptionParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'{order.id}' => $this->order()->getIncrementId()
				,'{shop.domain}' => $this->getStoreUri()->getHost()
				,'{shop.name}' => $this->store()->getName()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function iso3() {return $this->address()->getCountryModel()->getIso3Code();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @used-by Df_OnPay_Model_Request_Payment::_params()
	 * @used-by Df_RbkMoney_Model_Request_Payment::_params()
	 * @used-by Df_WebPay_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function localeCode() {return $this->configS()->getLocaleCodeInServiceFormat();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function nameFirst() {return $this->order()->getCustomerFirstname();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function nameLast() {return $this->order()->getCustomerLastname();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function nameMiddle() {return $this->order()->getCustomerMiddlename();}

	/**
	 * @override
	 * @see Df_Payment_Model_Request::order()
	 * @used-by Df_Payment_Model_Request::amount()
	 * @used-by Df_Payment_Model_Request::method()
	 * @used-by Df_Payment_Model_Request::payment()
	 * @return Df_Sales_Model_Order
	 */
	protected function order() {return df_last_order();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function phone() {return df_nts($this->address()->getTelephone());}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function postCode() {return df_nts($this->address()->getPostcode());}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	protected function preprocessParams(array $params) {return $this->chopParams($params);}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function regionCode() {return $this->region()->getCode();}

	/**
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function street() {return implode(' ', df_clean($this->address()->getStreet()));}

	/**
	 * Этот метод используют только потомки:
	 * @used-by Df_Interkassa_Model_Request_Payment::_params()
	 * @used-by Df_Kkb_Model_Request_Payment::_params()
	 * @used-by Df_LiqPay_Model_Request_Payment::getParamsForXml()
	 * @used-by Df_WebMoney_Model_Request_Payment::_params()
	 * @used-by Df_WebPay_Model_Request_Payment::_params()
	 * @return string
	 */
	protected function urlConfirm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getUrl($this->method()->getCode() . '/confirm');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by city()
	 * @used-by iso3()
	 * @used-by phone()
	 * @used-by region()
	 * @used-by street()
	 * @return Df_Sales_Model_Order_Address
	 */
	private function address() {return $this->order()->getBillingAddress();}

	/**
	 * @param string $text
	 * @param string $requestVarName
	 * @return string
	 */
	private function chopParam($text, $requestVarName) {
		df_param_string($text, 0);
		df_param_string($requestVarName, 1);
		/** @var int $maxLength */
		$maxLength = $this->method()->constManager()->requestVarMaxLength($requestVarName);
		/** @var string $result */
		$result =
			(0 >= $maxLength)
			? $text
			: mb_substr($text, 0, $maxLength, 'UTF-8')
		;
		return $result;
	}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	private function chopParams(array $params) {
		/** @var array(string => mixed) $result */
		$result = array();
		foreach ($params as $paramName => $paramValue) {
			/** @var string $paramName */
			/** @var mixed $paramValue */
			/**
			 * $paramName — всегда строка!
			 * $paramName нужно нам для того, чтобы заглянуть в config.xml
			 * и поискать там ограничение на длину $paramValue
			 */
			df_assert_string($paramName);
			$result[$paramName] =
				!is_string($paramValue)
				? $paramValue
				: $this->chopParam($paramValue, $paramName)
			;
		}
		return $result;
	}

	/**
	 * @used-by regionCode()
	 * @return Mage_Directory_Model_Region
	 */
	private function region() {return $this->address()->getRegionModel();}

	/**
	 * @used-by Df_Payment_Model_Method_WithRedirect::getPaymentPageParams()
	 * @param Df_Payment_Model_Method_WithRedirect $method
	 * @return array(string => string|int)
	 */
	public static function params(Df_Payment_Model_Method_WithRedirect $method) {
		/** @var array(string => array(string => string|int)) */
		static $cache;
		/** @var string $key */
		$key = $method->getCode();
		if (!isset($cache[$key])) {
			/** @var Df_Payment_Model_Request_Payment $i */
			$i = df_ic(df_con($method, 'Model_Request_Payment'), __CLASS__);
			$cache[$key] = $i->preprocessParams($i->_params());
		}
		return $cache[$key];
	}
}