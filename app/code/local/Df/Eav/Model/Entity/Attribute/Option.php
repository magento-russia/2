<?php
class Df_Eav_Model_Entity_Attribute_Option extends Mage_Eav_Model_Entity_Attribute_Option {
	/**
	 * Вынуждены сделать этот метод публичным, потому что публичен родительский.
	 * @see Mage_Eav_Model_Entity_Attribute_Option::_construct()
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(Df_Eav_Model_Resource_Entity_Attribute_Option::mf());
	}
	const _CLASS = __CLASS__;
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}