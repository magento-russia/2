<?php
class Df_Shipping_Model_Setup_2_16_3 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
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
		rm_eav_reset();
	}

	/** @return Df_Shipping_Model_Setup_2_16_3 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}