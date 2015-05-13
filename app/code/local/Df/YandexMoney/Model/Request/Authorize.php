<?php
/**
 * @method Df_YandexMoney_Model_Config_Area_Service getServiceConfig()
 */
class Df_YandexMoney_Model_Request_Authorize extends Df_YandexMoney_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'регистрации платежа в системе Яндекс.Деньги';
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsUnique() {
		return array(
			'pattern_id' => 'p2p'
			, 'to' => $this->getServiceConfig()->getShopId()
			/**
			 * Раньше здесь требовался ещё параметр
			 * 'identifier_type' => 'account'
			 * Теперь же в документации упоминание о нём отсутствует:
			 * @link http://api.yandex.ru/money/doc/dg/reference/request-payment.xml
			 */
			, $this->getAmountFieldName() => $this->getAmount()->getAsString()
			, 'comment' => $this->getRequestPayment()->getTransactionDescription()
			, 'message' => $this->getRequestPayment()->getTransactionDescriptionForShop()
			, 'label' => $this->getRequestPayment()->getTransactionTag()
		);
	}

	/**
	 * @@override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		/**
		 * На самом деле, данный запрос является не вторичным, а первичным,
		 * поэтому внешний идентификатор платежа у нас ещё отсутствует.
		 * Мы унаследовали данный класс от @see Df_YandexMoney_Model_Request_Secondary
		 * и @see Df_Payment_Model_Request_Secondary просто ради удобства.
		 * Видимо, семантика класса @see Df_Payment_Model_Request_Secondary
		 * на данный момент не вполне соответствует его названию.
		 */
		df_should_not_be_here(__METHOD__);
		return '';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestType() {return 'request-payment';}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {return Df_YandexMoney_Model_Response_Authorize::_CLASS;}

	/** @return string */
	private function getAmountFieldName() {
		return $this->getServiceConfig()->isFeePayedByBuyer() ? 'amount_due' : 'amount';
	}

	/** @return Df_YandexMoney_Model_Request_Payment */
	private function getRequestPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMoney_Model_Request_Payment::i($this->getOrder());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_YandexMoney_Model_Payment $paymentMethod
	 * @param Mage_Sales_Model_Order_Payment $orderPayment
	 * @param string $token
	 * @return Df_YandexMoney_Model_Request_Authorize
	 */
	public static function i(
		Df_YandexMoney_Model_Payment $paymentMethod
		, Mage_Sales_Model_Order_Payment $orderPayment
		, $token
	) {
		return new self(array(
			self::P__PAYMENT_METHOD => $paymentMethod
			, self::P__ORDER_PAYMENT => $orderPayment
			, self::P__TOKEN => $token
		));
	}
}