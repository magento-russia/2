<?php
class Df_1C_Model_Cml2_State_Export_Products extends Df_Varien_Data_Collection {
	/**
	 * @param int $productId
	 * @return Df_Catalog_Model_Product
	 */
	public function getProductById($productId) {
		df_param_integer($productId, 0);
		/** @var Df_Catalog_Model_Product $result */
		$result = $this->getItemById($productId);
		if (!$result) {
			$result = df_product($productId, rm_state()->getStoreProcessed()->getId());
			$this->addItem($result);
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_1C_Model_Cml2_State_Export_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}