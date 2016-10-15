<?php
class Df_IPay_Model_Action_ConfirmPaymentByShop extends Df_IPay_Model_Action_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestAsXml_Test() {
		return df_1251_to("<?xml version='1.0' encoding='windows-1251' ?>
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
		");
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		$this->checkPaymentAmount();
		$this->e()->appendChild(df_xml_node('TransactionStart')->importArray(array(
			'ServiceProvider_TrxId' => $this->order()->getIncrementId()
			,'Info' => array('InfoLine' => $this->getRequestPayment()->getTransactionDescription())
		)));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedRequestType() {return self::$TRANSACTION_STATE__START;}

	/**
	 * @return void
	 * @throws Mage_Core_Exception
	 */
	private function checkPaymentAmount() {
		if (
				$this->getRequestParam_PaymentAmount()->getAsInteger()
			!==
				$this->getPaymentAmountFromOrder()->getAsInteger()
		) {
			df_error(
				$this->getMessage(
					Df_Payment_Model_Action_Confirm::CONFIG_KEY__MESSAGE__INVALID__PAYMENT_AMOUNT
				)
				,$this->getPaymentAmountFromOrder()->getAsInteger()
				,$this->configS()->getCurrencyCode()
				,$this->getRequestParam_PaymentAmount()->getAsInteger()
				,$this->configS()->getCurrencyCode()
			);
		}
	}

	/** @return Df_Core_Model_Money */
	protected function getPaymentAmountFromOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->configS()->getOrderAmountInServiceCurrency($this->order());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Money */
	protected function getRequestParam_PaymentAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_money($this->getRequestParam('TransactionStart/Amount'));
		}
		return $this->{__METHOD__};
	}
}