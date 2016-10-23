<?php
/**
 * @method Df_IPay_Method getMethod()
 * @method Df_IPay_Config_Area_Service configS()
 */
class Df_IPay_Request_Payment extends Df_Payment_Request_Payment {
	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @see Df_Payment_Request::amount()
	 * @used-by Df_IPay_Action_GetPaymentAmount::_process()
	 * @return Df_Core_Model_Money
	 */
	public function amount() {return parent::amount();}

	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @used-by Df_IPay_Action_GetPaymentAmount::_process()
	 * @used-by Df_IPay_Action_ConfirmPaymentByShop::_process()
	 * @used-by Df_IPay_Action_GetPaymentAmount::_process()
	 * @see Df_Payment_Request_Payment::description()
	 * @return string
	 */
	public function description() {return parent::description();}

	/**
	 * @override
	 * @see Df_Payment_Request_Payment::_params()
	 * @used-by Df_Payment_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			'srv_no' => $this->shopId()
			/**
			 * iPay допускает не больше 6 символов в номере платежа,
			 * поэтому используем @uses Mage_Sales_Model_Order::getId()
			 * вместо обычного @see Mage_Sales_Model_Order::getIncrementId()
			 */
			,'pers_acc' => $this->orderId()
			// iPay требует, чтобы суммы были целыми числами
			,'amount' => $this->amount()->getAsInteger()
			,'amount_editable' => 'N'
			// iPay и LiqPay, в отличие от других платёжных систем,
			// не поддерживают разные веб-адреса для успешного и неуспешного сценариев оплаты
			,'provider_url' => $this->getUrlReturn()
		);
	}

	/**
	 * @override
	 * @see Df_Payment_Request_Payment::order()
	 * @return Df_Sales_Model_Order
	 */
	protected function order() {return $this->_order ?: parent::order();}

	/**
	 * Без _nosid система будет формировать ссылку c ?___SID=U.
	 * На всякий случай избегаем этого.
	 * @return string
	 */
	private function getUrlReturn() {
		return Mage::getUrl($this->getMethod()->getCode() . '/customerReturn', array('_nosid' => true));
	}

	/**
	 * 2016-10-15
	 * @used-by Df_IPay_Action_Abstract::getRequestPayment()
	 * @param Df_Sales_Model_Order $o
	 * @return self
	 */
	public static function i(Df_Sales_Model_Order $o) {
		$result = new self;
		$result->_order = $o;
		return $result;
	}

	/**
	 * 2016-10-15
	 * @used-by i()
	 * @used-by order()
	 * @var Df_Sales_Model_Order
	 */
	private $_order;
}