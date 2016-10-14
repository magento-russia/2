<?php
class Df_Checkout_Model_Collection_Ergonomic_Address_Row extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return 'Df_Checkout_Block_Frontend_Ergonomic_Address_Row';}

	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Row */
	public static function i() {return new self;}
}