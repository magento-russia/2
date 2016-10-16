<?php
class Df_Checkout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {
	/**
	 * @override
	 * @param array $data
	 * @param int $customerAddressId
	 * @return array
	 */
	public function saveBilling($data, $customerAddressId) {
		/** @var array $result */
		try {
			$data = $this->getFilter()->filter($data);
			/**
			 * Может получиться такая ситуация,
			 * что после назначения поля обязательным
			 * найдутся ранее учтённые в системе покупатели,
			 * у которых это поле не заполнено.
			 *
			 * Это приведёт к сбою в методе Mage_Checkout_Model_Type_Onepage::_validateCustomerData
			 * Чтобы избежать такого сбоя, добавляем к данным покупателя
			 * указанные при оформлении заказа данные.
			 */
			if ($this->getQuote()->getCustomerId()) {
				/**
				 * 2013-11-06
				 * Раньше тут стоял код:
				 * $this->getQuote()->getCustomer()->addData($data);
				 * Он неверен!
				 * Например, у зарегистрированного покупателя заполнено поле email,
				 * При этом от браузера с экрана оформления заказа
				 * придет идентификатор покупателя,
				 * но значение поля email может прийти пустым
				 * (ведь покупатель не заполнял это поле,
				 * а выбрал ранее зарегистрированный адрес).
				 * Надо перед вызовом addData убирать пустые значения.
				 * Дефект допущен 1 марта 2012 года и замечен 6 ноября 2013 года
				 * (почему-то он полтора года не проявлялся: видимо, он проявляется не всегда,
				 * а зависит от каких-то дополнительных условий
				 * или изменений других учсастков кода)
				 */
				$this->getQuote()->getCustomer()->addData(df_clean($data));
			}
			$result = parent::saveBilling($data, $customerAddressId);
		}
		catch (Exception $e) {
			$result = array('error' => -1, 'message' => df_ets($e));
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/** @return Zend_Filter */
	private function getFilter() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Filter $result */
			$this->{__METHOD__} = new Zend_Filter();
			$this->{__METHOD__}->addFilter(Df_Checkout_Model_Filter_Ergonomic_SetDefaultPassword::i());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Checkout_Controller_Action $controller
	 * @return void
	 */
	public function setController(Mage_Checkout_Controller_Action $controller) {
		$this->_controller = $controller;
	}

	/** @return Df_Checkout_Model_Type_Onepage */
	private function getController() {return $this->_controller;}

	/** @var Mage_Checkout_Controller_Action */
	private $_controller;

	/**
	 * 2015-03-17
	 * Инициализируем объект именно так для совместимости с кодом ядра.
	 * @used-by app/design/frontend/rm/default/template/df/checkout/ergonomic/dashboard.phtml
	 * @used-by Df_CustomerBalance_Observer::sales_order_place_before()
	 * @used-by Df_Reward_Observer::sales_order_place_before()
	 * @return Df_Checkout_Model_Type_Onepage
	 */
	public static function s() {return Mage::getSingleton('checkout/type_onepage');}
}