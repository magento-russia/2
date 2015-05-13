<?php
class Df_Checkout_Model_Collection_Ergonomic_Address_Row extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Checkout_Block_Frontend_Ergonomic_Address_Row::_CLASS;}
	const _CLASS = __CLASS__;
	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Row */
	public static function i() {return new self;}
}