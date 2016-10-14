<?php
class Df_Sales_Model_Quote_Item extends Mage_Sales_Model_Quote_Item {
	const _C = __CLASS__;
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Quote_Item
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}