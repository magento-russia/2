<?php
/**
 * @method Df_Eav_Model_Entity_Attribute_Set|null findByLabel(string $label)
 */
class Df_Dataflow_Model_Registry_Collection_AttributeSets extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Set_Collection
	 */
	protected function createCollection() {
		/** @var Df_Eav_Model_Resource_Entity_Attribute_Set_Collection $result */
		$result = Df_Eav_Model_Resource_Entity_Attribute_Set_Collection::i();
		$result->setEntityTypeFilter(rm_eav_id_product());
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Eav_Model_Entity_Attribute_Set::_CLASS;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Eav_Model_Entity_Attribute_Set $entity
	 * @return string|null
	 */
	protected function getEntityLabel(Mage_Core_Model_Abstract $entity) {
		return $entity->getAttributeSetName();
	}

	/** @return Df_Dataflow_Model_Registry_Collection_AttributeSets */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}