<?php
/**
 * @method Df_Psbank_Model_Config_Area_Service getServiceConfig()
 */
class Df_Psbank_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return string
	 */
	public function getTransactionDescription() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_substr(parent::getTransactionDescription(), 0, 50);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		return array_merge(
			$this->getParamsForSignature()
			,array('P_SIGN' => $this->getSignature())
		);
	}

	/** @return string */
	private function getOrderId() {
		/** @var string $result */
		$result = $this->getOrder()->getIncrementId();
		df_result_string_not_empty($result);
		// Согласно документации, номер заказа должен содежать только цифры
		// и состоять не менее чем из 6 символов
		df_assert(ctype_digit($result));
		df_assert_ge(6, strlen($result));
		return $result;
	}

	/** @return array(string => string) */
	private function getParamsForSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'AMOUNT' => $this->getAmount()->getAsString()
				, 'CURRENCY' => 'RUB'
				, 'ORDER' => $this->getOrderId()
				, 'DESC' => $this->getTransactionDescription()
				, 'TERMINAL' => $this->getServiceConfig()->getTerminalId()
				, 'TRTYPE' => $this->getTransactionType()
				, 'MERCH_NAME' => $this->getServiceConfig()->getShopName()
				, 'MERCHANT' => $this->getShopId()
				, 'EMAIL' => Df_Core_Helper_Mail::s()->getCurrentStoreMailAddress()
				, 'TIMESTAMP' => Df_Psbank_Helper_Data::s()->getTimestamp()
				, 'NONCE' => Df_Psbank_Helper_Data::s()->generateNonce()
				, 'BACKREF' => $this->getCustomerReturnUrl()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Psbank_Helper_Data::s()->generateSignature(
					$this->getParamsForSignature()
					,array(
						'AMOUNT', 'CURRENCY', 'ORDER', 'MERCH_NAME', 'MERCHANT', 'TERMINAL', 'EMAIL'
						, 'TRTYPE', 'TIMESTAMP', 'NONCE', 'BACKREF'
					)
					,$this->getServiceConfig()->getRequestPassword()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getTransactionType() {
		return rm_int($this->getServiceConfig()->isCardPaymentActionCapture());
	}
}