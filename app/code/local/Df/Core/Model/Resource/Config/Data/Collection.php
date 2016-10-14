<?php
class Df_Core_Model_Resource_Config_Data_Collection extends Mage_Core_Model_Mysql4_Config_Data_Collection {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Config_Data
	 */
	public function getResource() {return Df_Core_Model_Resource_Config_Data::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Core_Model_Config_Data::_C;}
	const _C = __CLASS__;
}