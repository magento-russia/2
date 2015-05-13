<?php
class Df_Banner_Model_Resource_Banner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Banner_Model_Banner::mf(), Df_Banner_Model_Resource_Banner::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Banner_Model_Resource_Banner_Collection */
	public static function i() {return new self;}
}