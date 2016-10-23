<?php
class Df_Shipping_Setup_2_16_3 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		/** @var string[] $attributeCodes */
		$attributeCodes = array(
			Df_Catalog_Model_Product::P__WIDTH
			,Df_Catalog_Model_Product::P__HEIGHT
			,Df_Catalog_Model_Product::P__LENGTH
		);
		foreach ($attributeCodes as $attributeCode) {
			/** @var string $attributeCode */
			self::attribute()->updateAttribute(
				$entityTypeId = Mage_Catalog_Model_Product::ENTITY
				,$id = $attributeCode
				,$field = 'is_user_defined'
				,$value = 0
			);
		}
		df_eav_reset();
	}
}