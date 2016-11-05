<?php
namespace Df\C1\Cml2\State\Export;
class Products extends \Df_Varien_Data_Collection {
	/**
	 * @param int $productId
	 * @return \Df_Catalog_Model_Product
	 */
	public function getProductById($productId) {
		df_param_integer($productId, 0);
		/** @var \Df_Catalog_Model_Product $result */
		$result = $this->getItemById($productId);
		if (!$result) {
			$result = df_product($productId, df_state()->getStoreProcessed()->getId());
			$this->addItem($result);
		}
		return $result;
	}

	/**
	 * @used-by \Df\C1\Cml2\State\Export::getProducts()
	 * @return self
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}