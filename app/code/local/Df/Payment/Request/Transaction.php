<?php
namespace Df\Payment\Request;
use Df_Core_Model_Money as Money;
use Mage_Sales_Model_Order_Payment as OP;
abstract class Transaction extends Secondary  {
	/**
	 * 2015-03-09
	 * Обратите внимание, что @uses hasAmount() возвращает true при выполнении операций:
	 * @see \Df\Payment\Method::capture()
	 * @see \Df\Payment\Method::refund()
	 * В обоих случаях $this->cfg(self::$P__AMOUNT) — это не валюта заказа,
	 * а базовая (учётная) валюта магазина.
	 * Смотрите комментарии к указанным методам.
	 * @override
	 * @see \Df\Payment\Request::amount()
	 * @return Money
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
	 * @see \Df\Payment\Method::capture()
	 * @see \Df\Payment\Method::refund()
	 * @used-by getAmount()
	 * @used-by \Df\Alfabank\Request\Secondary::getParams()
	 * @return bool
	 */
	protected function hasAmount() {return !!$this->cfg(self::$P__AMOUNT);}

	/**
	 * @param OP $payment
	 * @param float $amount [optional]
	 * @return void
	 */
	private function _doTransaction(OP $payment, $amount = 0.0) {
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
		 * @see \Df\Payment\Method::capture()
		 * @see \Df\Payment\Method::refund()
		 * но отсутствует для операции @see \Df\Payment\Method::void()
		 */
		$this->_prop(self::$P__AMOUNT, DF_V_FLOAT, false);
	}

	/**
	 * @used-by \Df\Payment\Method::doTransaction()
	 * @param string $type
	 * @param OP $payment
	 * @param float $amount [optional]
	 * @return void
	 */
	public static function doTransaction($type, OP $payment, $amount = 0.0) {
		/**
		 * Намеренно используем @uses ucfirst() вместо @see df_ucfirst()
		 * потому что в данном случае нам не нужна поддержка UTF-8.
		 * @var string $class
		 */
		$class = df_con($payment->getMethodInstance(), 'Request_' . ucfirst($type));
		/** @var $this $i */
		$i = df_ic($class, self::class);
		$i->_doTransaction($payment, $amount);
	}

	/** @var string */
	private static $P__AMOUNT = 'amount';
}


