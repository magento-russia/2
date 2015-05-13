<?php
class Df_Core_Model_Resource_Location_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Location::mf(), Df_Core_Model_Resource_Location::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Core_Model_Resource_Location_Collection */
	public static function i() {return new self;}
}