<?php
class Df_YandexMarket_Model_Setup_2_38_2 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		foreach (df()->registry()->attributeSets() as $attributeSet) {
			/** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
			Df_YandexMarket_Model_Setup_Processor_AttributeSet::process($attributeSet);
		}
	}

	/** @return Df_YandexMarket_Model_Setup_2_38_2 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}