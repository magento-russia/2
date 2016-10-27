<?php
class Df_Shipping_Setup_2_15_4 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		foreach (df()->registry()->attributeSets() as $attributeSet) {
			/** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
			\Df\Shipping\Processor\AddDimensionsToProductAttributeSet::process($attributeSet);
		}
		df_eav_reset();
	}
}