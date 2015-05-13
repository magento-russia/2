<?php
class Df_IPay_Model_Action_ConfirmPaymentByShop extends Df_IPay_Model_Action_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestAsXml_Test() {
		/** @var string $result */
		$result =
			df_text()->convertUtf8ToWindows1251("<?xml version='1.0' encoding='windows-1251' ?>
<ServiceProvider_Request>
	<Version>1</Version>
	<RequestType>TransactionStart</RequestType>
	<DateTime>20090124153856</DateTime>
	<PersonalAccount>2</PersonalAccount>
	<Currency>974</Currency>
	<RequestId>9221</RequestId>
	<TransactionStart>
		<Amount>1233700</Amount>
		<TransactionId>6180433</TransactionId>
		<Agent>999</Agent>
		<AuthorizationType>iPay</AuthorizationType>
	</TransactionStart>
</ServiceProvider_Request>
			")
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return Df_IPay_Model_Action_ConfirmPaymentByShop
	 */
	protected function processInternal() {
		$this->checkPaymentAmount();
		$this->getResponseAsSimpleXmlElement()
			->appendChild(
				Df_Varien_Simplexml_Element::createNode('TransactionStart')
					->importArray(
						array(
							'ServiceProvider_TrxId' => $this->getOrder()->getIncrementId()
							,'Info' =>
								array(
									'InfoLine' => $this->getRequestPayment()->getTransactionDescription()
								)
						)
					)
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedRequestType() {
		return self::TRANSACTION_STATE__START;
	}

	/**
	 * @return Df_IPay_Model_Action_ConfirmPaymentByShop
	 * @throws Mage_Core_Exception
	 */
	private function checkPaymentAmount() {
			df_assert(
					$this->getRequestParam_PaymentAmount()->getAsInteger()
				===
					$this->getPaymentAmountFromOrder()->getAsInteger()
				,rm_sprintf(
					$this->getMessage(
						Df_Payment_Model_Action_Confirm::CONFIG_KEY__MESSAGE__INVALID__PAYMENT_AMOUNT
					)
					,$this->getPaymentAmountFromOrder()->getAsInteger()
					,$this->getServiceConfig()->getCurrencyCode()
					,$this->getRequestParam_PaymentAmount()->getAsInteger()
					,$this->getServiceConfig()->getCurrencyCode()
				)
			)
		;
		return $this;
	}

	/** @return Df_Core_Model_Money */
	protected function getPaymentAmountFromOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getServiceConfig()->getOrderAmountInServiceCurrency(
				$this->getOrder()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Money */
	protected function getRequestParam_PaymentAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Money::i(
				rm_float($this->getRequestParam(self::REQUEST_PARAM__TRANSACTION_START__AMOUNT))
			);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const REQUEST_PARAM__TRANSACTION_START__AMOUNT = 'TransactionStart/Amount';
	/**
	 * @static
	 * @param Df_IPay_ConfirmPaymentByShopController $controller
	 * @return Df_IPay_Model_Action_ConfirmPaymentByShop
	 */
	public static function i(Df_IPay_ConfirmPaymentByShopController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}