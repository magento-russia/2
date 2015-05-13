<?php
class Df_Sales_Model_Quote_Address_Item extends Mage_Sales_Model_Quote_Address_Item {
	/**
	 * Цель перекрытия:
	 *
	 * 2014-10-06
	 * Magento CE 1.9.0.1
	 * Устраняем сбой
	 * «Notice: Undefined property: Mage_Sales_Model_Quote_Address_Item::$_optionsByCode
	 * in app/code/core/Mage/Sales/Model/Quote/Item/Abstract.php on line 90»
	 * @see Mage_Sales_Model_Quote_Item_Abstract::getProduct():
			if (is_array($this->_optionsByCode)) {
				$product->setCustomOptions($this->_optionsByCode);
			}
	 * Magento CE использует поле $_optionsByCode,
	 * хотя оно определено только в классе @see Mage_Sales_Model_Quote_Address_Item,
	 * но не в классе @see Mage_Sales_Model_Quote_Address_Item
	 *
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (!isset($this->_optionsByCode)) {
			$this->_optionsByCode = null;
		}
	}
}