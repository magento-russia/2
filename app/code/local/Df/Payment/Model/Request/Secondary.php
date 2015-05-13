<?php
abstract class Df_Payment_Model_Request_Secondary extends Df_Payment_Model_Request {
	/**
	 * Этот метод сделан публичным и вынесен в базовый класс,
	 * потому что этот метод используется для диагностики:
	 * @see Df_Payment_Exception_Response::getMessageRm()
	 * @return Zend_Uri_Http
	 */
	abstract public function getUri();

	/** @return string */
	abstract protected function getGenericFailureMessageUniquePart();

	/**
	 * Этот метод никак не используется данным классом,
	 * однако из смысла данного класса следует,
	 * что этот метод должен иметься у всех потомков данного класса,
	 * потому что данный класс предназначен для выполнения вторничного запроса к платёжной системе,
	 * и чтобы платёжная система могла сопоставить данный вторичный запрос первичному,
	 * потребуется идентификатор платежа.
	 * @abstract
	 * @return string
	 */
	abstract protected function getPaymentExternalId();

	/** @return array(string => string) */
	abstract protected function getResponseAsArray();

	/** @return string */
	abstract protected function getResponseClass();

	/**
	 * @override
	 * @return Df_Sales_Model_Order
	 */
	public function getOrder() {return $this->getOrderPayment()->getOrder();}

	/** @return Df_Payment_Model_Response */
	public function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model($this->getResponseClass(), $this->getResponseAsArray());
			df_assert($this->{__METHOD__} instanceof Df_Payment_Model_Response);
			/** @var Df_Payment_Model_Response $response */
			$response = $this->{__METHOD__};
			// для диагностики
			$response->setRequest($this);
			$response->postProcess($this->getOrderPayment());
		}
		return $this->{__METHOD__};
	}
	/** @return string */
	protected function getGenericFailureMessage() {
		return rm_sprintf(
			'При %s произошёл неизвестный сбой.', $this->getGenericFailureMessageUniquePart()
		);
	}

	/** @return Mage_Sales_Model_Order_Payment */
	protected function getOrderPayment() {return $this->cfg(self::P__ORDER_PAYMENT);}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ORDER_PAYMENT, 'Mage_Sales_Model_Order_Payment');
	}
	const _CLASS = __CLASS__;
	const P__ORDER_PAYMENT = 'order_payment';
}