<?php
/**
 * Cообщение:		«df_catalog__attribute_set__group_added»
 * Источник:		Df_Catalog_Helper_Product::addGroupToAttributeSetIfNeeded()
 */
class Df_Catalog_Model_Event_AttributeSet_GroupAdded extends Df_Core_Model_Event {
	/** @return int */
	public function getAttributeSetId() {return df_nat($this->getEventParam(self::$E__ATTRIBUTE_SET_ID));}

	/** @return Df_Eav_Model_Entity_Attribute_Set */
	public function getAttributeSet() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Eav_Model_Entity_Attribute_Set::ld($this->getAttributeSetId());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getGroupName() {return $this->getEventParam(self::$E__GROUP_NAME);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::$EVENT;}

	/** @var string */
	private static $E__ATTRIBUTE_SET_ID = 'attribute_set_id';
	/** @var string */
	private static $E__GROUP_NAME = 'group_name';
	/** @var string */
	private static $EVENT = 'df_catalog__attribute_set__group_added';

	/**
	 * @used-by Df_Catalog_Helper_Product::addGroupToAttributeSetIfNeeded()
	 * @param int $attributeSetId
	 * @param string $groupName
	 * @return void
	 */
	public static function dispatch($attributeSetId, $groupName) {
		df_param_integer($attributeSetId, 0);
		df_param_string_not_empty($groupName, 1);
		Mage::dispatchEvent(self::$EVENT, array(
			self::$E__ATTRIBUTE_SET_ID => $attributeSetId, self::$E__GROUP_NAME => $groupName
		));
	}

	/**
	 * @used-by Df_C1_Observer::df_catalog__attribute_set__group_added()
	 * @param Varien_Event_Observer $observer
	 * @return Df_Catalog_Model_Event_AttributeSet_GroupAdded
	 */
	public static function i(Varien_Event_Observer $observer) {return self::ic(__CLASS__, $observer);}
}