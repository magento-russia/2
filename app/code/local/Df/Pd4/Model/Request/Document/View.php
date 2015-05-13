<?php
class Df_Pd4_Model_Request_Document_View extends Df_Core_Model_Abstract {
	/** @return Df_Sales_Model_Order */
	public function getOrder() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Order $result */
			$result = Df_Sales_Model_Order::i();
			$result->load($this->getOrderId());
			if (rm_nat0($result->getId()) !== rm_nat0($this->getOrderId())) {
				df_error('Заказ №%d отсутствует в системе.', $this->getOrderId());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Money */
	public function getAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getServiceConfig()->getOrderAmountInServiceCurrency($this->getOrder())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Pd4_Model_Payment */
	public function getPaymentMethod() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Pd4_Model_Payment $result */
			$result = null;
			/**
			 * Раньше здесь стояло if(!is_null($this->getOrder()->getPayment()))
			 * Как ни странно, иногда $this->getOrder()->getPayment() возвращает и не null,
			 * и не объект.
			 */
			if ($this->getOrder()->getPayment() instanceof Mage_Sales_Model_Order_Payment) {
				$result = $this->getOrder()->getPayment()->getMethodInstance();
			}
			if (!($result instanceof Df_Pd4_Model_Payment)) {
				df_error(
					"Заказ №{$this->getOrderId()} не предназначен для оплаты через банковскую кассу."
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getOrderId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = null;
			try {
				$result =
					Df_Sales_Model_Resource_Order::s()->getOrderIdByProtectCode(
						$this->getOrderProtectCode()
					)
				;
			}
			catch (Exception $e) {
				df_error('Заказ с кодом «%s» отсутствует в системе.', $this->getOrderProtectCode());
			}
			try {
				$result = rm_nat($result);
			}
			catch (Exception $e) {
				df_error($this->getInvalidUrlMessage());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return integer */
	private function getOrderProtectCode() {
		/** @var integer $result */
		$result = df_request(Df_Pd4_Const::URL_PARAM__ORDER_PROTECT_CODE);
		df_assert(!is_null($result), $this->getInvalidUrlMessage());
		return $result;
	}

	/** @return string */
	private function getInvalidUrlMessage() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = 
				nl2br(
					df_h()->pd4()->__(
						"Вероятно, Вы хотели распечатать квитанцию?"
						."\nОднако ссылка на квитанцию не совсем верна."
						."\nМожет быть, Вы не полностью скопировали ссылку в адресную строку браузера?"
						."\nПопробуйте аккуратно ещё раз."
						."\nЕсли Вы вновь увидите данное сообщение — обратитесь к администратору магазина,"
						." приложив к вашему обращению ссылку на квитанцию."
					)
				)
			;
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Config_Area_Service */
	private function getServiceConfig() {return $this->getPaymentMethod()->getRmConfig()->service();}

	const _CLASS = __CLASS__;

	/** @return Df_Pd4_Model_Request_Document_View */
	public static function i() {return new self;}
}