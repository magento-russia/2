<?php
/** @method Df_Psbank_Model_Config_Area_Service configS() */
class Df_Psbank_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return string
	 */
	public function description() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_substr(parent::description(), 0, 50);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array('P_SIGN' => $this->getSignature()) + $this->getParamsForSignature();
	}

	/** @return array(string => string) */
	private function getParamsForSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'AMOUNT' => $this->amountS()
				, 'CURRENCY' => 'RUB'
				, 'ORDER' => $this->orderIId()
				, 'DESC' => $this->description()
				, 'TERMINAL' => $this->configS()->getTerminalId()
				, 'TRTYPE' => $this->getTransactionType()
				, 'MERCH_NAME' => $this->configS()->getShopName()
				, 'MERCHANT' => $this->shopId()
				, 'EMAIL' => df_store_mail_address()
				, 'TIMESTAMP' => Df_Psbank_Helper_Data::s()->getTimestamp()
				, 'NONCE' => Df_Psbank_Helper_Data::s()->generateNonce()
				, 'BACKREF' => $this->urlCustomerReturn()
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
					,$this->password()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getTransactionType() {
		return df_int($this->configS()->isCardPaymentActionCapture());
	}
}