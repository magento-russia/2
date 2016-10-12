<?php
abstract class Df_Payment_Model_Request extends Df_Core_Model {
	/** @return Df_Sales_Model_Order */
	abstract public function getOrder();

	/**
	 * 1) @see Df_Payment_Exception_Response::getMessageRm()
	 * использует этот метод для потомков @see Df_Payment_Model_Request_Secondary
	 * 2) @see Df_Payment_Model_Method_WithRedirect::getPaymentPageParams()
	 * использует этот метод для потомков @see Df_Payment_Model_Request_Payment
	 * 3) В то же время, потомки данного класса могут использовать этот метод
	 * для формирования запроса к серверу.
	 * @return array(string => string)
	 */
	abstract public function getParams();

	/** @return Df_Core_Model_Money */
	public function getAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getServiceConfig()->getOrderAmountInServiceCurrency($this->getOrder())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Config_Area_Service */
	public function getServiceConfig() {return $this->getPaymentMethod()->getRmConfig()->service();}

	/** @return Mage_Payment_Model_Info */
	protected function getPaymentInfoInstance() {return $this->getPaymentMethod()->getInfoInstance();}

	/**
	 * Обратите внимание, что класс-потомок @see Df_Payment_Model_Request_Payment
	 * перекрывает метод getPaymentMethod()
	 * (способен извлекать способ платежа из объекта-заказа),
	 * и, таком образом, для него не требуется передача способа платежа в конструкторе
	 * @return Df_Payment_Model_Method_Base
	 */
	protected function getPaymentMethod() {return $this->cfg(self::P__PAYMENT_METHOD);}

	/** @return string */
	protected function getShopId() {
		return $this->getPaymentMethod()->getRmConfig()->service()->getShopId();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Обратите внимание, что класс-потомок @see Df_Payment_Model_Request_Payment
		 * перекрывает метод getPaymentMethod()
		 * (способен извлекать способ платежа из объекта-заказа),
		 * и, таком образом, для него не требуется передача способа платежа в конструкторе
		 * @see Df_Payment_Model_Request_Payment::getPaymentMethod()
		 */
		$this->_prop(self::P__PAYMENT_METHOD, Df_Payment_Model_Method_Base::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__PAYMENT_METHOD = 'payment_method';
}