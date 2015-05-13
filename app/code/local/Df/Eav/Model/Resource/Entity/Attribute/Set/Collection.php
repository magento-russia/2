<?php
class Df_Eav_Model_Resource_Entity_Attribute_Set_Collection
	extends Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection {
	/**
	 * Вынуждены делать данный метод публичным,
	 * потому что родительский метод
	 * @see Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection::_construct()
	 * публичен в Magento CE 1.4.0.1
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(
			Df_Eav_Model_Entity_Attribute_Set::mf(), Df_Eav_Model_Resource_Entity_Attribute_Set::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_Eav_Model_Resource_Entity_Attribute_Set_Collection */
	public static function i() {return new self;}
}