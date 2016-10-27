<?php
namespace Df\IPay\Request;
use Df_Sales_Model_Order as O;
/**
 * @method \Df\IPay\Method method()
 * @method \Df\IPay\Config\Area\Service configS()
 */
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @see \Df\Payment\Request::amount()
	 * @used-by \Df\IPay\Action\GetPaymentAmount::_process()
	 * @return \Df_Core_Model_Money
	 */
	public function amount() {return parent::amount();}

	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @used-by \Df\IPay\Action\GetPaymentAmount::_process()
	 * @used-by \Df\IPay\Action\ConfirmPaymentByShop::_process()
	 * @used-by \Df\IPay\Action\GetPaymentAmount::_process()
	 * @see \Df\Payment\Request\Payment::description()
	 * @return string
	 */
	public function description() {return parent::description();}

	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
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
	 * @see \Df\Payment\Request\Payment::order()
	 * @return O
	 */
	protected function order() {return $this->_order ?: parent::order();}

	/**
	 * Без _nosid система будет формировать ссылку c ?___SID=U.
	 * На всякий случай избегаем этого.
	 * @return string
	 */
	private function getUrlReturn() {
		return \Mage::getUrl($this->method()->getCode() . '/customerReturn', array('_nosid' => true));
	}

	/**
	 * 2016-10-15
	 * @used-by \Df\IPay\Action::getRequestPayment()
	 * @param O $o
	 * @return self
	 */
	public static function i(O $o) {
		$result = new self;
		$result->_order = $o;
		return $result;
	}

	/**
	 * 2016-10-15
	 * @used-by i()
	 * @used-by order()
	 * @var O
	 */
	private $_order;
}