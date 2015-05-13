<?php
class Df_Admin_Model_ClassInfo_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Admin_Model_ClassInfo::_CLASS;}

	/** @return Df_Admin_Model_ClassInfo_Collection */
	public static function i() {return new self;}
} 