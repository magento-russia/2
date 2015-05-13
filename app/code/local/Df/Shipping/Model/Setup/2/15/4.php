<?php
class Df_Shipping_Model_Setup_2_15_4 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		foreach (df()->registry()->attributeSets() as $attributeSet) {
			/** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
			Df_Shipping_Model_Processor_AddDimensionsToProductAttributeSet::process($attributeSet);
		}
		/**
		 * Вот в таких ситуациях, когда у нас меняется структура прикладного типа товаров,
		 * нам нужно сбросить глобальный кэш EAV.
		 */
		rm_eav_reset();
	}

	/** @return Df_Shipping_Model_Setup_2_15_4 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}