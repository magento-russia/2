<?php
/**
 * Класс @see Mage_Sales_Model_Mysql4_Order_Status отсутствует в Magento CE 1.4
 * 2016-10-16
 * Magento CE 1.4 отныне не поддерживаем.
 */
class Df_Sales_Model_Order_Status extends Mage_Sales_Model_Order_Status {
	/** @return string|null */
	public function getLabel() {return $this->_getData(self::P__LABEL);}

	/**
	 * @override
	 * @return Df_Sales_Model_Resource_Order_Status_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
	 * @return Df_Sales_Model_Resource_Order_Status
	 */
	protected function _getResource() {return Df_Sales_Model_Resource_Order_Status::s();}

	/** @used-by Df_Sales_Model_Resource_Order_Status_Collection::_construct() */

	const P__LABEL = 'label';

	/** @return Df_Sales_Model_Resource_Order_Status_Collection */
	public static function c() {return new Df_Sales_Model_Resource_Order_Status_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Order_Status
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Sales_Model_Order_Status */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}