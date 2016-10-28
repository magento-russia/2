<?php
namespace Df\WalletOne\Request;
use Df\WalletOne\AddPaymentMethods as A;
use Df\WalletOne\Request\SignatureGenerator as G;
/** @method \Df\WalletOne\Method method() */
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array('WMI_SIGNATURE' => $this->getSignature()) + $this->paramsCommon();
	}

	/**
	 * @override
	 * @return string
	 */
	public function description() {
		return 'BASE64:' . base64_encode(parent::description());
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

	/** @return G */
	private function getSignatureGenerator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = G::i(array(
				G::P__ENCRYPTION_KEY => $this->password()
				,G::P__SIGNATURE_PARAMS => $this->getParamsForSignature()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int) */
	private function paramsCommon() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
				,self::REQUEST_VAR__ORDER_COMMENT => $this->description()
				,self::REQUEST_VAR__ORDER_CURRENCY =>
					$this->configS()->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
				// блокировать, но не списывать сразу средства с кошелька покупателя
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_ACTION => 0
				,self::REQUEST_VAR__SHOP_ID => $this->shopId()
				,self::REQUEST_VAR__URL_RETURN_OK => df_url_checkout_success()
				,self::REQUEST_VAR__URL_RETURN_NO => df_url_checkout_fail()
				,self::$METHODS_ENABLED =>
					A::i(array(
						A::P__FIELD_NAME => self::$METHODS_ENABLED
						,A::P__FIELD_VALUES => $this->configS()->getSelectedPaymentMethods()
					))
				,self::$METHODS_DISABLED =>
					A::i(array(
						A::P__FIELD_NAME => self::$METHODS_DISABLED
						,A::P__FIELD_VALUES => $this->configS()->getDisabledPaymentMethods()
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