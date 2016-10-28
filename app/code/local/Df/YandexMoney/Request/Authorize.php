<?php
namespace Df\YandexMoney\Request;
use Mage_Sales_Model_Order_Payment as OP;
/** @method \Df\YandexMoney\Config\Area\Service configS() */
class Authorize extends Secondary {
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
			, 'comment' => $this->getRequestPayment()->description()
			, 'message' => $this->getRequestPayment()->descriptionForShop()
			, 'label' => $this->getRequestPayment()->getTransactionTag()
		);
	}

	/**
	 * На самом деле, данный запрос является не вторичным, а первичным,
	 * поэтому внешний идентификатор платежа у нас ещё отсутствует.
	 * Мы унаследовали данный класс от @see \Df\YandexMoney\Request\Secondary
	 * и @see \Df\Payment\Request\Secondary просто ради удобства.
	 * Видимо, семантика класса @see \Df\Payment\Request\Secondary
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

	/** @return Payment */
	private function getRequestPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Payment::i($this->order());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by \Df\YandexMoney\Action\CustomerReturn::getRequestAuthorize()
	 * @param OP $orderPayment
	 * @param string $token
	 * @return self
	 */
	public static function i(OP $orderPayment, $token) {
		return new self(array(self::$P__PAYMENT => $orderPayment, self::P__TOKEN => $token));
	}
}