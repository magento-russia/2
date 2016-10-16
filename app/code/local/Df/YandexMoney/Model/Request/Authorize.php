<?php
/** @method Df_YandexMoney_Model_Config_Area_Service configS() */
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
			, 'to' => $this->shopId()
			/**
			 * Раньше здесь требовался ещё параметр
			 * 'identifier_type' => 'account'
			 * Теперь же в документации упоминание о нём отсутствует:
			 * http://api.yandex.ru/money/doc/dg/reference/request-payment.xml
			 */
			, $this->getAmountFieldName() => $this->amountS()
			, 'comment' => $this->getRequestPayment()->getTransactionDescription()
			, 'message' => $this->getRequestPayment()->getTransactionDescriptionForShop()
			, 'label' => $this->getRequestPayment()->getTransactionTag()
		);
	}

	/**
	 * На самом деле, данный запрос является не вторичным, а первичным,
	 * поэтому внешний идентификатор платежа у нас ещё отсутствует.
	 * Мы унаследовали данный класс от @see Df_YandexMoney_Model_Request_Secondary
	 * и @see Df_Payment_Model_Request_Secondary просто ради удобства.
	 * Видимо, семантика класса @see Df_Payment_Model_Request_Secondary
	 * на данный момент не вполне соответствует его названию.
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestType() {return 'request-payment';}

	/** @return string */
	private function getAmountFieldName() {
		return $this->configS()->isFeePayedByBuyer() ? 'amount_due' : 'amount';
	}

	/** @return Df_YandexMoney_Model_Request_Payment */
	private function getRequestPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMoney_Model_Request_Payment::i($this->order());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_YandexMoney_Model_Action_CustomerReturn::getRequestAuthorize()
	 * @param Mage_Sales_Model_Order_Payment $orderPayment
	 * @param string $token
	 * @return Df_YandexMoney_Model_Request_Authorize
	 */
	public static function i(Mage_Sales_Model_Order_Payment $orderPayment, $token) {
		return new self(array(self::$P__PAYMENT => $orderPayment, self::P__TOKEN => $token));
	}
}