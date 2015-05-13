<?php
class Df_Core_Model_Resource_Layout_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Вынуждены сделать метод публичным, потому что публичен родительский.
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Layout_Data::mf(), Df_Core_Model_Resource_Layout::mf());
	}

	/** @return Df_Core_Model_Resource_Layout_Collection */
	public static function i() {return new self;}
}
 