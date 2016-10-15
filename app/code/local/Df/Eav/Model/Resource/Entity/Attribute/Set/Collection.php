<?php
class Df_Eav_Model_Resource_Entity_Attribute_Set_Collection
	extends Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection {
	/**
	 * @override
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Set
	 */
	public function getResource() {return Df_Eav_Model_Resource_Entity_Attribute_Set::s();}

	/**
	 * Вынуждены делать данный метод публичным,
	 * потому что родительский метод
	 * @see Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection::_construct()
	 * публичен в Magento CE 1.4.0.1
	 * @override
	 * @return void
	 */
	public function _construct() {$this->_itemObjectClass = Df_Eav_Model_Entity_Attribute_Set::class;}

}