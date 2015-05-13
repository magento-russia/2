<?php
class Df_Core_Model_Resource_Website_Collection extends Mage_Core_Model_Mysql4_Website_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Website::mf(), Df_Core_Model_Resource_Website::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Core_Model_Resource_Website_Collection */
	public static function i() {return new self;}
}