<?php
namespace Df\YandexMoney\Request;
use Mage_Sales_Model_Order_Payment as OP;
/** @method \Df\YandexMoney\Config\Area\Service configS() */
class Capture extends Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'проведении платежа в системе Яндекс.Деньги';
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsUnique() {
		return array(
			'request_id' => $this->getPaymentExternalId()
			,'money_source' => 'wallet'
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		return $this->getResponseAuthorize()->getOperationExternalId();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestType() {return 'process-payment';}

	/** @return \Df\YandexMoney\Response\Authorize */
	private function getResponseAuthorize() {return $this->cfg(self::P__RESPONSE_AUTHORIZE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__RESPONSE_AUTHORIZE, \Df\YandexMoney\Response\Authorize::class);
	}
	const P__RESPONSE_AUTHORIZE = 'response_authorize';
	/**
	 * @used-by \Df\YandexMoney\Action\CustomerReturn::getRequestCapture()
	 * @param OP $orderPayment
	 * @param \Df\YandexMoney\Response\Authorize $responseAuthorize
	 * @param string $token
	 * @return self
	 */
	public static function i(
		OP $orderPayment
		, \Df\YandexMoney\Response\Authorize $responseAuthorize
		, $token
	) {
		return new self(array(
			self::$P__PAYMENT => $orderPayment
			, self::P__RESPONSE_AUTHORIZE => $responseAuthorize
			, self::P__TOKEN => $token
		));
	}
}