<?php
abstract class Df_Payment_Model_Request_Transaction extends Df_Payment_Model_Request_Secondary  {
	/**
	 * 2015-03-09
	 * Обратите внимание, что @uses hasAmount() возвращает true при выполнении операций:
	 * @see Df_Payment_Model_Method::capture()
	 * @see Df_Payment_Model_Method::refund()
	 * В обоих случаях $this->cfg(self::$P__AMOUNT) — это не валюта заказа,
	 * а базовая (учётная) валюта магазина.
	 * Смотрите комментарии к указанным методам.
	 * @override
	 * @see Df_Payment_Model_Request::amount()
	 * @return Df_Core_Model_Money
	 */
	protected function amount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->hasAmount()
				? parent::amount()
				: $this->configS()->convertAmountToServiceCurrency(
					$this->store()->getBaseCurrency(), $this->cfg(self::$P__AMOUNT)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @see doTransaction()
	 * Возвращает true при выполнении операций:
	 * @see Df_Payment_Model_Method::capture()
	 * @see Df_Payment_Model_Method::refund()
	 * @used-by getAmount()
	 * @used-by Df_Alfabank_Model_Request_Secondary::getParams()
	 * @return bool
	 */
	protected function hasAmount() {return !!$this->cfg(self::$P__AMOUNT);}

	/**
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @param float $amount [optional]
	 * @return void
	 */
	private function _doTransaction(Mage_Sales_Model_Order_Payment $payment, $amount = 0.0) {
		$this->addData(array(self::$P__PAYMENT => $payment, self::$P__AMOUNT => $amount));
		$this->getResponse();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * 2015-03-09
		 * Обратите внимание, что значение параметра self::$P__AMOUNT
		 * присутствует для операций
		 * @see Df_Payment_Model_Method::capture()
		 * @see Df_Payment_Model_Method::refund()
		 * но отсутствует для операции @see Df_Payment_Model_Method::void()
		 */
		$this->_prop(self::$P__AMOUNT, DF_V_FLOAT, false);
	}

	/**
	 * @used-by Df_Payment_Model_Method::doTransaction()
	 * @param string $type
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @param float $amount [optional]
	 * @return void
	 */
	public static function doTransaction(
		$type, Mage_Sales_Model_Order_Payment $payment, $amount = 0.0
	) {
		/**
		 * Намеренно используем @uses ucfirst() вместо @see df_ucfirst()
		 * потому что в данном случае нам не нужна поддержка UTF-8.
		 * @var string $class
		 */
		$class = df_con($payment->getMethodInstance(), 'Model_Request_' . ucfirst($type));
		/** @var Df_Payment_Model_Request_Transaction $i */
		$i = df_ic($class, 'Df_Payment_Model_Request_Transaction');
		$i->_doTransaction($payment, $amount);
	}

	/** @var string */
	private static $P__AMOUNT = 'amount';
}


