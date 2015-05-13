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
		$result = array();
		try {
			if (df_enabled(Df_Core_Feature::CHECKOUT)) {
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
			}
			$result = parent::saveBilling($data, $customerAddressId);
		}
		catch(Exception $e) {
			$result = array('error' => -1, 'message' => rm_ets($e));
			df_handle_entry_point_exception($e, true);
		}
		df_result_array($result);
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
	 * @return Mage_Checkout_Controller_Action
	 */
	public function setController(Mage_Checkout_Controller_Action $controller) {
		$this->_controller = $controller;
		return $this;
	}

	/** @return Df_Checkout_Model_Type_Onepage */
	private function getController() {return $this->_controller;}

	/** @var Mage_Checkout_Controller_Action */
	private $_controller;

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Model_Type_Onepage
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}