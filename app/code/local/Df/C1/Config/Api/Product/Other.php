<?php
class Df_C1_Config_Api_Product_Other extends Df_C1_Config_Api_Cml2 {
	/** @return boolean */
	public function showAttributesOnProductPage() {
		return $this->getYesNo('attributes__show_on_product_page');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__other/';}
	/** @return Df_C1_Config_Api_Product_Other */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}