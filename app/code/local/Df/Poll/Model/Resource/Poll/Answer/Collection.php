<?php
class Df_Poll_Model_Resource_Poll_Answer_Collection
	extends Mage_Poll_Model_Mysql4_Poll_Answer_Collection {
	/**
	 * Вынуждены сделать метод публичным, потому что публичен родительский.
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(Df_Poll_Model_Poll_Answer::mf(), Df_Poll_Model_Resource_Poll_Answer::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Poll_Model_Resource_Poll_Answer_Collection */
	public static function i() {return new self;}
} 