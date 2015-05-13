<?php
class Df_Core_Model_Resource_Config_Data_Collection
	extends Mage_Core_Model_Mysql4_Config_Data_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Config_Data::mf(), Df_Core_Model_Resource_Config_Data::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Core_Model_Resource_Config_Data_Collection */
	public static function i() {return new self;}
}