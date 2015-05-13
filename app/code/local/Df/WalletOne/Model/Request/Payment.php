<?php
/**
 * @method Df_WalletOne_Model_Payment getPaymentMethod()
 */
class Df_WalletOne_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string|int)
	 */
	public function getParams() {
		/** @var array(string => string|int) $result */
		$result =
			array_merge(
				parent::getParams()
				,array(
					self::REQUEST_VAR__SIGNATURE =>	$this->getSignature()
				)
			)
		;
		df_result_array($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionDescription() {
		return 'BASE64:' . base64_encode(parent::getTransactionDescription());
	}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string|int) $result */
		$result =
			array(
				self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
				,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
				,self::REQUEST_VAR__ORDER_CURRENCY =>
					$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
				// блокировать, но не списывать сразу средства с кошелька покупателя
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_ACTION => 0
				,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
				,self::REQUEST_VAR__URL_RETURN_OK => $this->getUrlCheckoutSuccess()
				,self::REQUEST_VAR__URL_RETURN_NO => $this->getUrlCheckoutFail()
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__ENABLED =>
					Df_WalletOne_Model_Form_Processor_AddPaymentMethods::i(
						array(
							Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_NAME =>
								self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__ENABLED
							,Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_VALUES =>
								$this->getServiceConfig()->getSelectedPaymentMethods()
						)
					)
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__DISABLED =>
					Df_WalletOne_Model_Form_Processor_AddPaymentMethods::i(
						array(
							Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_NAME =>
								self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__DISABLED
							,Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_VALUES =>
								$this->getServiceConfig()->getDisabledPaymentMethods()
						)
					)
			)
		;
		df_result_array($result);
		return $result;
	}

	/** @return array(string => string|int) */
	private function getParamsForSignature() {
		/** @var array(string => string|int) $result */
		$result =
			array_merge(
				$this->preprocessParams(
					array_merge(
						$this->getParamsInternal()
						,array(
							self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__ENABLED =>
								$this->getServiceConfig()->getSelectedPaymentMethods()
							,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__DISABLED =>
								$this->getServiceConfig()->getDisabledPaymentMethods()
						)
					)
				)
				,array(
					'form_key' => rm_session_core()->getFormKey()
				)
			)
		;
		df_result_array($result);
		return $result;
	}

	/** @return string */
	private function getSignature() {
		return $this->getSignatureGenerator()->getSignature();
	}

	/** @return Df_WalletOne_Model_Request_SignatureGenerator */
	private function getSignatureGenerator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_WalletOne_Model_Request_SignatureGenerator::i(array(
				Df_WalletOne_Model_Request_SignatureGenerator::P__ENCRYPTION_KEY =>
					$this->getServiceConfig()->getResponsePassword()
				,Df_WalletOne_Model_Request_SignatureGenerator::P__SIGNATURE_PARAMS =>
					$this->getParamsForSignature()
			));
		}
		return $this->{__METHOD__};
	}

	const REQUEST_VAR__ORDER_AMOUNT = 'WMI_PAYMENT_AMOUNT';
	const REQUEST_VAR__ORDER_COMMENT = 'WMI_DESCRIPTION';
	const REQUEST_VAR__ORDER_CURRENCY = 'WMI_CURRENCY_ID';
	const REQUEST_VAR__ORDER_NUMBER = 'WMI_PAYMENT_NO';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_ACTION = 'WMI_AUTO_ACCEPT';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__ENABLED = 'WMI_PTENABLED';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD__DISABLED = 'WMI_PTDISABLED';
	const REQUEST_VAR__SIGNATURE = 'WMI_SIGNATURE';
	const REQUEST_VAR__SHOP_ID = 'WMI_MERCHANT_ID';
	const REQUEST_VAR__URL_RETURN_OK = 'WMI_SUCCESS_URL';
	const REQUEST_VAR__URL_RETURN_NO = 'WMI_FAIL_URL';
}