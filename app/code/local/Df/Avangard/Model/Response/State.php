<?php
class Df_Avangard_Model_Response_State extends Df_Avangard_Model_Response {
	/** @return string */
	public function getAuthCode() {return $this->cfg('auth_code');}

	/** @return int */
	public function getPaymentStatus() {return $this->cfg(self::$P_STATUS_CODE);}

	/** @return string */
	public function getPaymentStatusDateAsText() {return $this->cfg('status_date');}
	
	/** @return Zend_Date */
	public function getPaymentStatusDate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Date($this->getPaymentStatusDateAsText(), Zend_Date::ISO_8601);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getPaymentStatusMeaning() {
		return dfa(
			array(
				0 => 'Заказ не найден'
				,1 => 'Обрабатывается'
				,2 => 'Отбракован'
				,3 => 'Исполнен'
			)
			, $this->getPaymentStatus()
			, 'Неизвестно'
		);
	}

	/** @return string */
	public function getPaymentStatusMessage() {return $this->cfg('status_desc');}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT;}

	/** @return string */
	public function getVerificationMethod() {return $this->cfg('method_name');}

	/** @return string */
	public function getVerificationMethodMeaning() {
		return
			dfa(
				array(
					'CVV' => 'операция подтверждена посредством ввода кода CVV2/CVC2'
					,'D3S' => 'операция подтверждена посредством 3D Secure (Verified by Visa/MasterCard Secure Code)'
					,'SCR' => 'поперация подтверждена посредством ввода кода со скретч-карты (данный способ доступен только для карт Банка Авангард)'
				)
				,$this->getVerificationMethod()
				,'неизвестно'
			)
		;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_filter(array(
				'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
				,'Состояние платежа' => $this->onSucc($this->getPaymentStatusMeaning())
				,'Описание состояния платежа' => $this->onFail($this->getPaymentStatusMessage())
				,'Способ подтверждения платежа' => $this->onSucc($this->getVerificationMethodMeaning())
				,'Дата и время платежа' => df_dts($this->getPaymentStatusDate(), 'dd.MM.y HH:mm:ss')
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isPaymentServiceError() {
		// Помечаем транзакцию закрытой,
		// только если деньги с покупателя списаны.
		return 'Внутренняя ошибка системы' === $this->getErrorMessage();
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {
		// Помечаем транзакцию закрытой,
		// только если деньги с покупателя списаны.
		return 3 === $this->getPaymentStatus();
	}

	/**
	 * @override
	 * @return void
	 * @throws Df_Payment_Exception_Response
	 */
	public function throwOnFailure() {
		if (!$this->isPaymentServiceError()) {
			parent::throwOnFailure();
			if (!$this->isTransactionClosed()) {
				$this->throwException($this->getPaymentStatusMeaning());
			}
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P_STATUS_CODE, DF_V_NAT0);
	}
	/** @var string */
	private static $P_STATUS_CODE = 'status_code';
}