<?php
abstract class Df_Payment_Model_Request_Payment extends Df_Payment_Model_Request {
	/**
	 * @abstract
	 * @return array(string => mixed)
	 */
	abstract protected function getParamsInternal();

	/**
	 * Метод публичен, потому что его использует класс Df_IPay_Model_Action_GetPaymentAmount
	 * @return Mage_Sales_Model_Order_Address
	 */
	public function getBillingAddress() {return $this->getOrder()->getBillingAddress();}

	/**
	 * @override
	 * @return Df_Sales_Model_Order
	 */
	public function getOrder() {return $this->cfg(self::P__ORDER);}

	/**
	 * Этот метод используется методом
	 * @see Df_Payment_Model_Method_WithRedirect::getPaymentPageParams()
	 * @override
	 * @return array(string => mixed)
	 */
	public function getParams() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $result */
			$this->{__METHOD__} = $this->preprocessParams($this->getParamsInternal());
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Метод публичен, потому что его иногда используют сторонние классы:
	 * @see Df_IPay_Model_Action_Confirm::processInternal()
	 * @see Df_IPay_Model_Action_ConfirmPaymentByShop::processInternal()
	 * @see Df_IPay_Model_Action_GetPaymentAmount::processInternal()
	 * @see Df_YandexMoney_Model_Request_Authorize::getRequestParams()
	 * @return string
	 */
	public function getTransactionDescription() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				str_replace(
					array_keys($this->getTransactionDescriptionParams())
					,array_values($this->getTransactionDescriptionParams())
					,$this->getServiceConfig()->getTransactionDescription()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getAddressStreet() {
		return implode(' ', df_clean($this->getBillingAddress()->getStreet()));
	}

	/** @return string */
	protected function getCustomerEmail() {return $this->getOrder()->getCustomerEmail();}

	/** @return string */
	public function getCustomerIpAddress() {
		return
			!is_null(rm_state()->getController())
			? rm_state()->getController()->getRequest()->getClientIp()
			: ''
		;
	}

	/** @return string */
	protected function getCustomerNameFull() {
		return rm_concat_clean(' '
			,$this->getOrder()->getCustomerLastname()
			,$this->getOrder()->getCustomerFirstname()
			,$this->getOrder()->getCustomerMiddlename()
		);
	}

	/** @return string */
	protected function getCustomerPhone() {return df_nts($this->getBillingAddress()->getTelephone());}

	/** @return string */
	protected function getCustomerReturnUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getPaymentMethod()->getCustomerReturnUrl($this->getOrder());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Payment_Model_Method_WithRedirect
	 */
	protected function getPaymentMethod() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Payment_Model_Method_WithRedirect $result */
			$result = parent::_getData(self::P__PAYMENT_METHOD);
			if (is_null($result)) {
				$result = $this->getOrder()->getPayment()->getMethodInstance();
			}
			if (!($result instanceof Df_Payment_Model_Method_WithRedirect)) {
				df_error(
					'Заказ №«%s» не предназначен для оплаты каким-либо из платёжных модулей
					Российской сборки Magento.'
					,$this->getOrder()->getIncrementId()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Store */
	protected function getStore() {return $this->getOrder()->getStore();}

	/** @return Zend_Uri_Http */
	protected function getStoreUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$this->{__METHOD__} = Zend_Uri_Http::fromString(
				$this->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	protected function getTransactionDescriptionParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'{order.id}' => $this->getOrder()->getIncrementId()
				,'{shop.domain}' => $this->getStoreUri()->getHost()
				,'{shop.name}' => $this->getStore()->getName()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getUrlCheckoutFail() {return df_h()->payment()->url()->getCheckoutFail();}

	/** @return string */
	protected function getUrlCheckoutSuccess() {
		return df_h()->payment()->url()->getCheckoutSuccess();
	}

	/** @return string */
	protected function getUrlConfirm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getUrl(df_concat_url(
				$this->getPaymentMethod()->getCode(), self::URL_PART__CONFIRM
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	protected function preprocessParams(array $params) {return $this->chopParams($params);}

	/**
	 * @param string $text
	 * @param string $requestVarName
	 * @return string
	 */
	private function chopParam($text, $requestVarName) {
		df_param_string($text, 0);
		df_param_string($requestVarName, 1);
		/** @var int $maxLength */
		$maxLength =
			$this->getPaymentMethod()->getRmConfig()->getConstManager()
				->getRequestVarMaxLength($requestVarName)
		;
		df_assert_integer($maxLength);
		/** @var string $result */
		$result =
			(0 >= $maxLength)
			? $text
			: mb_substr($text, 0, $maxLength, Df_Core_Const::UTF_8)
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
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ORDER, Df_Sales_Model_Order::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__ORDER = 'order';
	const URL_PART__CONFIRM = 'confirm';
}