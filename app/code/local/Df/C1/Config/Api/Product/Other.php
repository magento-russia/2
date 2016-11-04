<?php
namespace Df\C1\Config\Api\Product;
class Other extends \Df\C1\Config\Api\Cml2 {
	/** @return boolean */
	public function showAttributesOnProductPage() {
		return $this->getYesNo('attributes__show_on_product_page');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__other/';}
	/** @return \Df\C1\Config\Api\Product\Other */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}