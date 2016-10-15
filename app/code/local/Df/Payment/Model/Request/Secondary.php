<?php
abstract class Df_Payment_Model_Request_Secondary extends Df_Payment_Model_Request {
	/**
	 * Этот метод сделан публичным и вынесен в базовый класс,
	 * потому что этот метод используется для диагностики:
	 * @used-by Df_Payment_Exception_Response::message()
	 * @return Zend_Uri_Http
	 */
	abstract public function getUri();

	/**
	 * @used-by getGenericFailureMessage()
	 * @return string
	 */
	abstract protected function getGenericFailureMessageUniquePart();

	/**
	 * @override
	 * @used-by params()
	 * @return array(string => string|int)
	 */
	abstract protected function _params();

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

	/**
	 * @override
	 * @see Df_Payment_Model_Request::order()
	 * @used-by Df_Payment_Model_Request::amount()
	 * @used-by Df_Payment_Model_Request::method()
	 * @used-by Df_Payment_Model_Request::payment()
	 * @return Df_Sales_Model_Order
	 */
	protected function order() {return $this->payment()->getOrder();}

	/**
	 * 2015-03-15
	 * Основное назначение данного метода — подготовка параметров для запроса.
	 * Потомки его переопределяют через @see _params(), потомки же его и используют.
	 * Ядро Российской сборки Magento использует данный метод только для диагностики;
	 * наличие этого метода в базовом классе позволяет обобщить диагностику:
	 * @used-by Df_Payment_Exception_Response::message()
	 * @return array(string => string|int)
	 */
	public function params() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->_params();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Response */
	public function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $response = Df_Payment_Model_Response::ic(
				$this, $this->getResponseAsArray()
			);
			$response->postProcess($this->payment());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getGenericFailureMessage() {
		return sprintf('При %s произошёл неизвестный сбой.', $this->getGenericFailureMessageUniquePart());
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Request::payment()
	 * @used-by Df_Payment_Model_Request::method()
	 * @return Mage_Sales_Model_Order_Payment
	 */
	protected function payment() {return $this->cfg(self::$P__PAYMENT);}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PAYMENT, 'Mage_Sales_Model_Order_Payment');
	}
	/** @used-by Df_Kkb_Model_RequestDocument_Secondary::_construct() */

	/**
	 * @used-by _construct()
	 * @used-by getPayment()
	 * @used-by ic()
	 * @used-by Df_Payment_Model_Request_Transaction::doTransaction()
	 * @used-by Df_YandexMoney_Model_Request_Authorize::i()
	 * @used-by Df_YandexMoney_Model_Request_Capture::i()
	 * @var string
	 */
	protected static $P__PAYMENT = 'payment';

	/**
	 * @used-by Df_Alfabank_Model_Request_State::i()
	 * @used-by Df_Avangard_Model_Request_State::i()
	 * @param string $class
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @return Df_Payment_Model_Request_Secondary
	 */
	protected static function ic($class, Mage_Sales_Model_Order_Payment $payment) {
		return df_ic($class, __CLASS__, array(self::$P__PAYMENT => $payment));
	}
}