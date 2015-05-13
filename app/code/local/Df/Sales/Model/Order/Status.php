<?php
class Df_Sales_Model_Order_Status extends Mage_Sales_Model_Order_Status {
	/** @return string|null */
	public function getLabel() {return $this->_getData(self::P__LABEL);}

	/**
	 * @param string $value
	 * @return Df_Sales_Model_Order_Status
	 */
	public function setLabel($value) {
		df_param_string($value, 0);
		$this->setData(self::P__LABEL, $value);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (!self::isOldInterface()) {
			$this->_init(Df_Sales_Model_Resource_Order_Status::mf());
		}
	}
	const _CLASS = __CLASS__;
	const P__LABEL = 'label';

	/** @return Df_Sales_Model_Resource_Order_Status_Collection */
	public static function c() {
		if (self::isOldInterface()) {
			df_error(
				'Этот метод нельзя вызывать в данной версии Magento Community Edition.'
				."\nВам надо обновить Magento Community Edition"
				." либо обратиться в службу поддежки Российской сборки Magento."
			);
		}
		return self::s()->getCollection();
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Order_Status
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Sales_Model_Resource_Order_Status_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Sales_Model_Order_Status */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * Класс @see Mage_Sales_Model_Mysql4_Order_Status отсутствует в Magento CE 1.4.
	 * @return bool
	 */
	private static function isOldInterface() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			$result = !@class_exists('Mage_Sales_Model_Mysql4_Order_Status');
		}
		return $result;
	}
}