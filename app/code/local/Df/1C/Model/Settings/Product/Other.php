<?php
class Df_1C_Model_Settings_Product_Other extends Df_1C_Model_Settings_Cml2 {
	/** @return boolean */
	public function showAttributesOnProductPage() {
		return $this->getYesNo('attributes__show_on_product_page');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__other/';}
	/** @return Df_1C_Model_Settings_Product_Other */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}