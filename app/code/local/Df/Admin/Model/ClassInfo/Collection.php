<?php
class Df_Admin_Model_ClassInfo_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Admin_Model_ClassInfo::class;}

	/** @return Df_Admin_Model_ClassInfo_Collection */
	public static function i() {return new self;}
} 