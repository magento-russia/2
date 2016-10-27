<?php
namespace Df\Avangard\Response;
use Mage_Sales_Model_Order_Payment_Transaction as T;
class State extends \Df\Avangard\Response {
	/** @return string */
	public function getAuthCode() {return $this->cfg('auth_code');}

	/** @return int */
	public function getPaymentStatus() {return $this->cfg(self::$P_STATUS_CODE);}

	/** @return string */
	public function getPaymentStatusDateAsText() {return $this->cfg('status_date');}
	
	/** @return \Zend_Date */
	public function getPaymentStatusDate() {return dfc($this, function() {return
		new \Zend_Date($this->getPaymentStatusDateAsText(), \Zend_Date::ISO_8601)
	;});}

	/** @return string */
	public function getPaymentStatusMeaning() {return dfa(
		['Заказ не найден', 'Обрабатывается', 'Отбракован', 'Исполнен']
		, $this->getPaymentStatus()
		, 'Неизвестно'
	);}

	/** @return string */
	public function getPaymentStatusMessage() {return $this->cfg('status_desc');}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return T::TYPE_PAYMENT;}

	/** @return string */
	public function getVerificationMethod() {return $this->cfg('method_name');}

	/** @return string */
	public function getVerificationMethodMeaning() {return dfa([
		'CVV' => 'операция подтверждена посредством ввода кода CVV2/CVC2'
		,'D3S' => 'операция подтверждена посредством 3D Secure (Verified by Visa/MasterCard Secure Code)'
		,'SCR' => 'поперация подтверждена посредством ввода кода со скретч-карты (данный способ доступен только для карт Банка Авангард)'
	], $this->getVerificationMethod(), 'неизвестно');}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {return dfc($this, function() {return array_filter([
		'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
		,'Состояние платежа' => $this->onSucc($this->getPaymentStatusMeaning())
		,'Описание состояния платежа' => $this->onFail($this->getPaymentStatusMessage())
		,'Способ подтверждения платежа' => $this->onSucc($this->getVerificationMethodMeaning())
		,'Дата и время платежа' => df_dts($this->getPaymentStatusDate(), 'dd.MM.y HH:mm:ss')
	]);});}

	/**.
	 * @override
	 * @return bool
	 */
	public function isPaymentServiceError() {return
		'Внутренняя ошибка системы' === $this->getErrorMessage()
	;}

	/**
	 * Помечаем транзакцию закрытой, только если деньги с покупателя списаны.
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {return 3 === $this->getPaymentStatus();}

	/**
	 * @override
	 * @return void
	 * @throws \Df\Payment\Exception\Response
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