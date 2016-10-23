<?php
class Df_IPay_TransactionState extends Df_Core_Model {
	/** @return Df_IPay_TransactionState */
	public function clear() {
		$this->getPayment()
			->unsAdditionalInformation(self::PAYMENT_PARAM__STATE)
			->save()
		;
		return $this;
	}

	/** @return string|null */
	public function get() {
		return $this->getPayment()->getAdditionalInformation(self::PAYMENT_PARAM__STATE);
	}

	/** @return Df_IPay_TransactionState */
	public function restore() {
		if ($this->_previousState) {
			$this->update($this->_previousState);
		}
		return $this;
	}

	/**
	 * @param string $newState
	 * @return Df_IPay_TransactionState
	 */
	public function update($newState) {
		df_param_string($newState, 0);
		$this->_previousState = $this->get();
		// Обратите внимание, что хранить состояние транзации в сессии было бы неправильно:
		// это не защищает при одновременной оплате одного заказа несколькими пользователями
		$this->getPayment()
			->setAdditionalInformation(self::PAYMENT_PARAM__STATE, $newState)
			->save()
		;
		return $this;
	}
	/** @var string|null */
	private $_previousState = null;

	/** @return Mage_Payment_Model_Info */
	private function getPayment() {return $this->cfg(self::P__PAYMENT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PAYMENT, 'Mage_Payment_Model_Info');
	}

	const P__PAYMENT = 'payment';
	const PAYMENT_PARAM__STATE = 'df_ipay__transaction_state';
	/**
	 * @static
	 * @param Mage_Payment_Model_Info $paymentInfo
	 * @return Df_IPay_TransactionState
	 */
	public static function i(Mage_Payment_Model_Info $paymentInfo) {
		return new self(array(self::P__PAYMENT => $paymentInfo));
	}
}