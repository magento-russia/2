<?php
abstract class Df_Payment_Model_Handler extends Df_Core_Model {
	/**
	 * @return void
	 * @throws Exception
	 */
	abstract protected function handleInternal();

	/**
	 * @override
	 * @return Df_Payment_Model_Handler
	 * @throws Exception
	 */
	public function handle() {
		try {
			$this->handleInternal();
		}
		catch (Exception $exception) {
			$this->getMethod()->logFailureLowLevel($exception);
			if ($exception instanceof Df_Core_Exception) {
				/** @var Df_Core_Exception $exception */
				$this->getMethod()->logFailureHighLevel($exception->getMessageRm());
			}
			rm_exception_to_session($exception);
			/**
			 * Перевозбуждаем исключительную ситуацию,
			 * потому что в случае неуспеха нам нужно прервать
			 * выполнение родительского метода
			 * @see Mage_Sales_Model_Order_Payment::capture()
			 * @see Mage_Sales_Model_Order_Payment::refund()
			 */
			df_error(
				'При обработке платежа объектом класса «%s» произошёл описанный выше сбой.'
				, get_class($this)
			);
		}
		return $this;
	}

	/** @return float */
	protected function getAmount() {return $this->cfg(self::P__AMOUNT);}

	/** @return Df_Payment_Model_Method_Base */
	protected function getMethod() {return $this->cfg(self::P__METHOD);}

	/** @return Df_Sales_Model_Order */
	protected function getOrder() {
		return $this->getOrderPayment()->getOrder();
	}

	/** @return Mage_Sales_Model_Order_Payment */
	protected function getOrderPayment() {return $this->cfg(self::P__ORDER_PAYMENT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__AMOUNT, self::V_FLOAT);
		$this->_prop(self::P__METHOD, Df_Payment_Model_Method_Base::_CLASS);
		$this->_prop(self::P__ORDER_PAYMENT, 'Mage_Sales_Model_Order_Payment');
	}
	const _CLASS = __CLASS__;
	const P__AMOUNT = 'amount';
	const P__METHOD = 'method';
	const P__ORDER_PAYMENT = 'order_payment';
}