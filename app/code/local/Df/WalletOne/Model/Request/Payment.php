<?php
/** @method Df_WalletOne_Model_Payment getMethod() */
class Df_WalletOne_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array('WMI_SIGNATURE' => $this->getSignature()) + $this->paramsCommon();
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionDescription() {
		return 'BASE64:' . base64_encode(parent::getTransactionDescription());
	}

	/** @return array(string => string|int) */
	private function getParamsForSignature() {
		return
				array('form_key' => df_session_core()->getFormKey())
			+
				$this->preprocessParams(array(
					self::$METHODS_ENABLED => $this->configS()->getSelectedPaymentMethods()
					,self::$METHODS_DISABLED => $this->configS()->getDisabledPaymentMethods()
				) + $this->paramsCommon())
		;
	}

	/** @return string */
	private function getSignature() {return $this->getSignatureGenerator()->getSignature();}

	/** @return Df_WalletOne_Model_Request_SignatureGenerator */
	private function getSignatureGenerator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_WalletOne_Model_Request_SignatureGenerator::i(array(
				Df_WalletOne_Model_Request_SignatureGenerator::P__ENCRYPTION_KEY =>
					$this->password()
				,Df_WalletOne_Model_Request_SignatureGenerator::P__SIGNATURE_PARAMS =>
					$this->getParamsForSignature()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int) */
	private function paramsCommon() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
				,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
				,self::REQUEST_VAR__ORDER_CURRENCY =>
					$this->configS()->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
				// блокировать, но не списывать сразу средства с кошелька покупателя
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_ACTION => 0
				,self::REQUEST_VAR__SHOP_ID => $this->shopId()
				,self::REQUEST_VAR__URL_RETURN_OK => rm_url_checkout_success()
				,self::REQUEST_VAR__URL_RETURN_NO => rm_url_checkout_fail()
				,self::$METHODS_ENABLED =>
					Df_WalletOne_Model_Form_Processor_AddPaymentMethods::i(array(
						Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_NAME =>
							self::$METHODS_ENABLED
						,Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_VALUES =>
							$this->configS()->getSelectedPaymentMethods()
					))
				,self::$METHODS_DISABLED =>
					Df_WalletOne_Model_Form_Processor_AddPaymentMethods::i(array(
						Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_NAME =>
							self::$METHODS_DISABLED
						,Df_WalletOne_Model_Form_Processor_AddPaymentMethods::P__FIELD_VALUES =>
							$this->configS()->getDisabledPaymentMethods()
					))
			);
		}
		return $this->{__METHOD__};
	}

	const REQUEST_VAR__ORDER_AMOUNT = 'WMI_PAYMENT_AMOUNT';
	const REQUEST_VAR__ORDER_COMMENT = 'WMI_DESCRIPTION';
	const REQUEST_VAR__ORDER_CURRENCY = 'WMI_CURRENCY_ID';
	const REQUEST_VAR__ORDER_NUMBER = 'WMI_PAYMENT_NO';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_ACTION = 'WMI_AUTO_ACCEPT';
	const REQUEST_VAR__SHOP_ID = 'WMI_MERCHANT_ID';
	const REQUEST_VAR__URL_RETURN_OK = 'WMI_SUCCESS_URL';
	const REQUEST_VAR__URL_RETURN_NO = 'WMI_FAIL_URL';

	/** @var string */
	private static $METHODS_DISABLED = 'WMI_PTDISABLED';
	/** @var string */
	private static $METHODS_ENABLED = 'WMI_PTENABLED';
}