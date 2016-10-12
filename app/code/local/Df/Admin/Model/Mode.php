<?php
class Df_Admin_Model_Mode extends Df_Core_Model {
	/** @return void */
	public function begin() {
		$this->_counter++;
		if (1 === $this->_counter) {
			$this->_currentStore = Mage::app()->getStore();
			$this->_updateMode = Mage::app()->getUpdateMode();
			/**
			 * Очень важный момент!
			 * Если Magento находится в режиме обновления,
			 * то Mage_Core_Model_App::getStore()
			 * всегда будет возвращать Mage_Core_Model_App::getDefaultStore(),
			 * даже для такого кода: Mage_Core_Model_App::getStore(999).
			 * Это приводит к весьма некорректному поведению системы в некоторых ситуациях,
			 * когда мы обновляем товарные разделы своим установочным скриптом:
			 * @see Mage_Catalog_Model_Resource_Abstract::_saveAttributeValue():
			 * $storeId = (int)Mage::app()->getStore($object->getStoreId())->getId();
			 * Этот код заведомо вернёт неправильный результат!
			 */
			Mage::app()->setUpdateMode(false);
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		}
	}

	/**
	 * @param object $object
	 * @param string $method
	 * @param array(string => mixed) $parameters [optional]
	 * @return mixed
	 * @throws Exception
	 */
	public function call($object, $method, array $parameters = array()) {
		/** @var mixed $result */
		$this->begin();
		try {
			$result = call_user_func_array(array($object, $method), $parameters);
		}
		catch (Exception $e) {
			$this->end();
			throw $e;
		}
		$this->end();
		return $result;
	}

	/** @return void */
	public function end() {
		df_assert_gt0($this->_counter);
		$this->_counter--;
		if (0 === $this->_counter) {
			Mage::app()->setCurrentStore($this->_currentStore);
			Mage::app()->setUpdateMode($this->_updateMode);
			unset($this->_currentStore);
			unset($this->_updateMode);
		}
	}

	/** @var Mage_Core_Model_Store */
	private $_currentStore;
	/** @var int */
	private $_counter = 0;
	/** @var bool */
	private $_updateMode;

	/** @return Df_Admin_Model_Mode */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}