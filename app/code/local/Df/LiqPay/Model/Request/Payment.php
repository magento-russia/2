<?php
/**
 * @method Df_LiqPay_Model_Payment getPaymentMethod()
 */
class Df_LiqPay_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getParams() {
		return
			array_merge(
				parent::getParams()
				,array(self::REQUEST_VAR__SIGNATURE =>	$this->getSignature())
			)
		;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		return array('operation_xml' => $this->getXmlEncoded());
	}

	/** @return array(string => string) */
	private function getParamsForXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result =
				array(
					'version' => '1.2'
					,self::REQUEST_VAR__CUSTOMER__PHONE =>
						df_concat(
							'+'
							,Df_Core_Model_Format_MobilePhoneNumber::i($this->getCustomerPhone())
								->getOnlyDigits()
						)
					,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
					,self::REQUEST_VAR__ORDER_COMMENT =>
						/**
						 * Раньше LiqPay запрещал кириллицу в описании платежа,
						 * но теперь, вроде, разрешает.
						 */
						$this->getTransactionDescription()
					,self::REQUEST_VAR__ORDER_CURRENCY =>
						$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
					,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
					,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
					,self::REQUEST_VAR__URL_CONFIRM => $this->getUrlConfirm()
					,self::REQUEST_VAR__URL_RETURN =>
						/**
						 * LiqPay, в отличие от других платёжных систем,
						 * не поддерживает разные веб-адреса
						 * для успешного и неуспешного сценариев оплаты
						 */
						$this->getUrlReturn()
				)
			;
			if ($this->getPaymentMethod()->getSubmethod()) {
				$result[self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHODS] =
					$this->getPaymentMethod()->getSubmethod()
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		return base64_encode(sha1(df_concat(
			$this->getServiceConfig()->getResponsePassword()
			,$this->getXml()
			,$this->getServiceConfig()->getResponsePassword()
		),1));
	}

	/** @return string */
	private function getUrlReturn() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Mage::getUrl(
					df_concat_url($this->getPaymentMethod()->getCode(), 'customerReturn')
					// Без _nosid система будет формировать ссылку c ?___SID=U.
					// На всякий случай избегаем этого.
					,array('_nosid' => true)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getXmlAsVarienObject()->toXml(
					// все свойства
					$arrAttributes = array()
					// корневой тэг
					, $rootName = 'request'
					/* не добавлять <?xml version="1.0" encoding="UTF-8"?> */
					, $addOpenTag = false
					// запрещаем добавление CDATA,
					// потому что LiqPay эту синтаксическую конструкцию не понимает
					, $addCdata = false
				)
			;
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Object */
	private function getXmlAsVarienObject() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Varien_Object($this->getParamsForXml());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getXmlEncoded() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = base64_encode($this->getXml());
		}
		return $this->{__METHOD__};
	}

	const REQUEST_VAR__CUSTOMER__PHONE = 'default_phone';
	const REQUEST_VAR__ORDER_AMOUNT = 'amount';
	const REQUEST_VAR__ORDER_COMMENT = 'description';
	const REQUEST_VAR__ORDER_CURRENCY = 'currency';
	const REQUEST_VAR__ORDER_NUMBER = 'order_id';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHODS = 'pay_way';
	const REQUEST_VAR__SIGNATURE = 'signature';
	const REQUEST_VAR__SHOP_ID = 'merchant_id';
	const REQUEST_VAR__URL_CONFIRM = 'server_url';
	const REQUEST_VAR__URL_RETURN = 'result_url';
}