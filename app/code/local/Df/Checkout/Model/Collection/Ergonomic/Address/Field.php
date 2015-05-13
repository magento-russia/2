<?php
class Df_Checkout_Model_Collection_Ergonomic_Address_Field extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Checkout_Block_Frontend_Ergonomic_Address_Field::_CLASS;}
	const _CLASS = __CLASS__;
	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	public static function i() {return new self;}
}